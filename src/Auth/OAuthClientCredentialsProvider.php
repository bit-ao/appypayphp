<?php

namespace Bit\AppyPay\Auth;

use Bit\AppyPay\AccessToken;
use Bit\AppyPay\TokenStoragePort;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class OAuthClientCredentialsProvider
{
    private Client $http;

    public function __construct(
        private readonly string           $tokenUrl,
        private readonly OAuthCredentials $creds,
        private readonly TokenStoragePort $store,
    ) {
        $this->http = new Client(['timeout' => 10]);
    }

    public function getToken(): AccessToken
    {
        $cached = $this->store->get();
        if ($cached !== null && !$cached->isExpired()) {
            return $cached;
        }

        try {
            $response = $this->http->post($this->tokenUrl, [
                'headers' => ['Content-Type' => 'application/x-www-form-urlencoded'],
                'form_params' => [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => $this->creds->clientId,
                    'client_secret' => $this->creds->clientSecret,
                    'resource'      => $this->creds->resource,
                ],
            ]);
        } catch (GuzzleException $e) {
            throw new \RuntimeException('BAD AUTHENTICATION: ' . $e->getMessage(), 0, $e);
        }

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException('BAD AUTHENTICATION');
        }

        $data  = json_decode((string) $response->getBody(), true);
        $token = AccessToken::fromResponse($data);
        $this->store->set($token);

        return $token;
    }

    public function forceRefresh(): AccessToken
    {
        $this->store->clear();
        return $this->getToken();
    }

    /**
     * Executa uma operação HTTP autenticada. Se a operação levantar
     * RequestException com 401, força refresh do token (cached pode estar
     * revogado do lado do servidor apesar de localmente parecer válido)
     * e re-tenta UMA vez. O segundo 401 propaga sem terceira tentativa.
     *
     * Assume idempotência da operação (refazer o mesmo POST/GET devolve
     * o mesmo resultado) — todos os endpoints autenticados desta lib
     * são idempotentes via referenceNumber / merchantTransactionId.
     *
     * @template T
     * @param  callable(string $bearerToken): T  $do
     * @return T
     */
    public function withToken(callable $do): mixed
    {
        $token = $this->getToken();
        try {
            return $do($token->accessToken);
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            if ($status !== 401) {
                throw $e;
            }
            // Token cached pode estar revogado. Força refresh + retry uma vez.
            $token = $this->forceRefresh();
            return $do($token->accessToken);
        }
    }
}
