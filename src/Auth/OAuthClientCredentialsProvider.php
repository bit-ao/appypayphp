<?php

namespace Bit\AppyPay\Auth;

use Bit\AppyPay\AccessToken;
use Bit\AppyPay\TokenStoragePort;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

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
}
