<?php

namespace Bit\AppyPay;

use Bit\AppyPay\Auth\OAuthClientCredentialsProvider;
use Bit\AppyPay\Auth\OAuthCredentials;
use Bit\AppyPay\Dtos\BaseChargeDto;
use Bit\AppyPay\Dtos\CreateChargeResponseDto;
use Bit\AppyPay\Dtos\CreateQrChargeResponseDto;
use Bit\AppyPay\Dtos\PaymentInfoEtpaDto;
use Bit\AppyPay\Dtos\PaymentInfoGpoDto;
use Bit\AppyPay\Dtos\PaymentInfoRefDto;
use Bit\AppyPay\Dtos\QrChargeDto;
use Bit\AppyPay\Dtos\RegisterReferenceDto;
use Bit\AppyPay\Dtos\RegisterReferenceResponseDto;
use Bit\AppyPay\Enums\PaymentMethod;
use Bit\AppyPay\Exception\AppyPayException;
use Bit\AppyPay\Storage\DiskTokenStorage;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

class AppyPay
{
    private Client $client;
    private OAuthClientCredentialsProvider $tokenProvider;
    private ?PaymentMethodsConfig $methods;
    private ?string $posCode;

    public function __construct(AppyPayConfig $config, ?TokenStoragePort $storage = null)
    {
        $this->methods = $config->methods;
        $this->posCode = $config->posCode ?? (getenv('APPYPAY_POS_CODE') ?: null);

        $creds = new OAuthCredentials(
            clientId:     $config->clientId     ?? (getenv('APPYPAY_CLIENT_ID')     ?: ''),
            clientSecret: $config->clientSecret ?? (getenv('APPYPAY_CLIENT_SECRET') ?: ''),
            resource:     $config->resource     ?? (getenv('APPYPAY_RESOURCE')      ?: ''),
        );

        $store = $storage ?? new DiskTokenStorage();
        $this->tokenProvider = new OAuthClientCredentialsProvider(
            $config->authUrl ?? (getenv('APPYPAY_AUTH_URL') ?: ''),
            $creds,
            $store,
        );

        $baseUrl = rtrim($config->baseUrl ?? (getenv('APPYPAY_API_URL') ?: ''), '/')
            . '/' . ($config->version ?? (getenv('APPYPAY_VERSION') ?: ''));

        $this->client = new Client([
            'base_uri' => rtrim($baseUrl, '/') . '/',
            'headers'  => [
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ],
            'timeout'  => 10,
        ]);
    }

    public static function fromEnv(?TokenStoragePort $storage = null): self
    {
        $config          = new AppyPayConfig();
        $methods         = new PaymentMethodsConfig();
        $methods->gpo    = getenv('APPYPAY_GPO')  ?: null;
        $methods->ref    = getenv('APPYPAY_REF')  ?: null;
        $methods->etpa   = getenv('APPYPAY_ETPA') ?: null;
        $config->methods = $methods;

        return new self($config, $storage);
    }

    public function auth(): AccessToken
    {
        return $this->tokenProvider->getToken();
    }

    /**
     * Dispatches to the appropriate charge method based on paymentMethod.
     * For QR charges, use chargeQr() directly to get CreateQrChargeResponseDto.
     */
    public function charge(BaseChargeDto $input): ChargeResponseInterface
    {
        return match ($input->paymentMethod) {
            PaymentMethod::express,
            PaymentMethod::aexpress => $this->chargeExpress($input),
            PaymentMethod::ref      => $this->chargeRef($input),
            PaymentMethod::qr       => $this->chargeQr($input),
            default                 => throw new \InvalidArgumentException('Unsupported payment method'),
        };
    }

    public function chargeExpress(BaseChargeDto $input): CreateChargeResponseDto
    {
        self::validate($input);
        $token   = $this->auth();
        $headers = ['Authorization' => 'Bearer ' . $token->accessToken];

        if ($input->paymentMethod === PaymentMethod::aexpress) {
            $headers['Accept'] = 'application/vnd.appypay.asyncapi+json';
        }

        try {
            $response = $this->client->post('charges', [
                'headers' => $headers,
                'json'    => $this->buildChargeBody($input),
            ]);

            return CreateChargeResponseDto::fromArray(
                json_decode((string) $response->getBody(), true)
            );
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $body   = $e->hasResponse()
                ? json_decode((string) $e->getResponse()->getBody(), true)
                : null;
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', $body, $status);
        } catch (GuzzleException $e) {
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', null, null);
        }
    }

    public function chargeRef(BaseChargeDto $input): CreateChargeResponseDto
    {
        self::validate($input);
        $token = $this->auth();

        try {
            $response = $this->client->post('charges', [
                'headers' => ['Authorization' => 'Bearer ' . $token->accessToken],
                'json'    => $this->buildChargeBody($input),
            ]);

            return CreateChargeResponseDto::fromArray(
                json_decode((string) $response->getBody(), true)
            );
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $body   = $e->hasResponse()
                ? json_decode((string) $e->getResponse()->getBody(), true)
                : null;
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', $body, $status);
        } catch (GuzzleException $e) {
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', null, null);
        }
    }

    /**
     * Registers one or more permanent payment references at AppyPay.
     * The merchant chooses the referenceNumber (9-15 digits, starting with 0
     * by convention for auto-generated). Once registered, the reference accepts
     * payments within the [minAmount, maxAmount] range until expirationDate.
     * Each payment fires a webhook to the merchant's configured endpoint.
     *
     * If $input->paymentMethod is empty, falls back to the REF identifier
     * configured via APPYPAY_REF env var.
     */
    public function registerReference(RegisterReferenceDto $input): RegisterReferenceResponseDto
    {
        if ($input->paymentMethod === '') {
            $input->paymentMethod = $this->methods?->ref ?? '';
        }

        self::validateRegisterReference($input);
        $token = $this->auth();

        try {
            $response = $this->client->post('references', [
                'headers' => ['Authorization' => 'Bearer ' . $token->accessToken],
                'json'    => $input->toArray(),
            ]);

            return RegisterReferenceResponseDto::fromArray(
                json_decode((string) $response->getBody(), true)
            );
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $body   = $e->hasResponse()
                ? json_decode((string) $e->getResponse()->getBody(), true)
                : null;
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', $body, $status);
        } catch (GuzzleException $e) {
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', null, null);
        }
    }

    private static function validateRegisterReference(RegisterReferenceDto $input): void
    {
        if ($input->paymentMethod === '') {
            throw new \InvalidArgumentException(
                'paymentMethod é obrigatório (configure APPYPAY_REF ou passe explicitamente)'
            );
        }
        if (empty($input->references)) {
            throw new \InvalidArgumentException('references[] não pode estar vazio');
        }
        foreach ($input->references as $i => $ref) {
            if (!preg_match('/^\d{9,15}$/', $ref->referenceNumber)) {
                throw new \InvalidArgumentException(
                    "references[$i].referenceNumber inválido: {$ref->referenceNumber} (9-15 dígitos numéricos)"
                );
            }
            if ($ref->minAmount !== null && $ref->maxAmount !== null && $ref->minAmount > $ref->maxAmount) {
                throw new \InvalidArgumentException(
                    "references[$i]: minAmount não pode ser maior que maxAmount"
                );
            }
        }
    }

    public function chargeQr(BaseChargeDto $input): CreateQrChargeResponseDto
    {
        // Always use posCode from config for QR charges
        $etpa          = new PaymentInfoEtpaDto();
        $etpa->posCode = $this->posCode ?? '';
        $input->paymentInfo = $etpa;

        self::validate($input);
        $token = $this->auth();

        $body = [
            'currency'              => 'AOA',
            'amount'                => $input->amount,
            'description'           => $input->description,
            'merchantTransactionId' => $input->merchantTransactionId,
            'paymentMethod'         => $this->resolvePaymentMethod($input->paymentMethod),
            'paymentInfo'           => ['posCode' => $this->posCode],
            'qrCodeType'            => $input instanceof QrChargeDto ? $input->qrCodeType : 'SINGLE',
        ];

        if ($input instanceof QrChargeDto) {
            if ($input->minAmount      !== null) $body['minAmount']       = $input->minAmount;
            if ($input->maxTransactions !== null) $body['maxTransactions'] = $input->maxTransactions;
            if ($input->startDate      !== null) $body['startDate']       = $input->startDate;
            if ($input->endDate        !== null) $body['endDate']         = $input->endDate;
        }

        try {
            $response = $this->client->post('qr-codes', [
                'headers' => ['Authorization' => 'Bearer ' . $token->accessToken],
                'json'    => $body,
            ]);

            return CreateQrChargeResponseDto::fromArray(
                json_decode((string) $response->getBody(), true)
            );
        } catch (RequestException $e) {
            $status = $e->hasResponse() ? $e->getResponse()->getStatusCode() : null;
            $body   = $e->hasResponse()
                ? json_decode((string) $e->getResponse()->getBody(), true)
                : null;
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', $body, $status);
        } catch (GuzzleException $e) {
            throw new AppyPayException($e->getMessage(), 'HTTP_ERROR', null, null);
        }
    }

    private static function validate(BaseChargeDto $input): void
    {
        if ($input->paymentInfo === null) {
            throw new \InvalidArgumentException(
                "paymentInfo é obrigatório para método {$input->paymentMethod->value}"
            );
        }

        switch ($input->paymentMethod) {
            case PaymentMethod::aexpress:
            case PaymentMethod::express:
                if (!($input->paymentInfo instanceof PaymentInfoGpoDto)) {
                    throw new \InvalidArgumentException(
                        'paymentInfo deve ser PaymentInfoGpoDto para express/aexpress'
                    );
                }
                if (!preg_match('/^\d{9,15}$/', $input->paymentInfo->phoneNumber)) {
                    throw new \InvalidArgumentException(
                        'phoneNumber inválido para GPO (9-15 dígitos)'
                    );
                }
                break;

            case PaymentMethod::ref:
                if (!($input->paymentInfo instanceof PaymentInfoRefDto)) {
                    throw new \InvalidArgumentException(
                        'paymentInfo deve ser PaymentInfoRefDto para ref'
                    );
                }
                if (!preg_match('/^\d{9,15}$/', $input->paymentInfo->referenceNumber)) {
                    throw new \InvalidArgumentException(
                        'referenceNumber inválido para REF (9-15 dígitos numéricos)'
                    );
                }
                if ($input->paymentInfo->dueDate !== null
                    && strtotime($input->paymentInfo->dueDate) === false
                ) {
                    throw new \InvalidArgumentException(
                        'dueDate inválido para REF (ISO 8601 esperado)'
                    );
                }
                break;

            case PaymentMethod::qr:
                if (!($input->paymentInfo instanceof PaymentInfoEtpaDto)) {
                    throw new \InvalidArgumentException(
                        'paymentInfo deve ser PaymentInfoEtpaDto para qr'
                    );
                }
                if (!preg_match('/^[A-Za-z0-9]{6,9}$/', $input->paymentInfo->posCode)) {
                    throw new \InvalidArgumentException(
                        'posCode inválido para eTPA (6-9 alfanumérico)'
                    );
                }
                break;

            default:
                throw new \InvalidArgumentException(
                    "Método de pagamento desconhecido: {$input->paymentMethod->value}"
                );
        }
    }

    private function resolvePaymentMethod(PaymentMethod $method): ?string
    {
        if ($this->methods === null) {
            throw new \RuntimeException('Payment methods config not initialized!');
        }

        return match ($method) {
            PaymentMethod::express,
            PaymentMethod::aexpress,
            PaymentMethod::qr   => $this->methods->gpo,
            PaymentMethod::ref  => $this->methods->ref,
            default             => throw new \InvalidArgumentException(
                "Método de pagamento não suportado: {$method->value}"
            ),
        };
    }

    private function buildChargeBody(BaseChargeDto $input): array
    {
        return [
            'currency'              => 'AOA',
            'amount'                => $input->amount,
            'description'           => $input->description,
            'merchantTransactionId' => $input->merchantTransactionId,
            'paymentMethod'         => $this->resolvePaymentMethod($input->paymentMethod),
            'paymentInfo'           => $this->paymentInfoToArray($input->paymentMethod, $input->paymentInfo),
        ];
    }

    private function paymentInfoToArray(PaymentMethod $method, mixed $info): array
    {
        if ($method === PaymentMethod::qr) {
            return ['posCode' => $this->posCode];
        }
        if ($info instanceof PaymentInfoGpoDto)  return $info->toArray();
        if ($info instanceof PaymentInfoRefDto)  return $info->toArray();
        if ($info instanceof PaymentInfoEtpaDto) return $info->toArray();
        return [];
    }
}
