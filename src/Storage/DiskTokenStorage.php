<?php

namespace Bit\AppyPay\Storage;

use Bit\AppyPay\AccessToken;
use Bit\AppyPay\TokenStoragePort;

class DiskTokenStorage implements TokenStoragePort
{
    private string $path;

    public function __construct(?string $path = null)
    {
        $this->path = $path ?? sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'appypay_oauth_token.json';
        $dir = dirname($this->path);
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
    }

    public function get(): ?AccessToken
    {
        if (!file_exists($this->path)) {
            return null;
        }

        $raw = @file_get_contents($this->path);
        if ($raw === false) {
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }

        return AccessToken::fromResponse($data);
    }

    public function set(AccessToken $token): void
    {
        $payload = json_encode([
            'token_type'   => $token->tokenType,
            'access_token' => $token->accessToken,
            'expires_on'   => $token->expiresOn,
            'resource'     => $token->resource,
        ]);

        file_put_contents($this->path, $payload, LOCK_EX);
        chmod($this->path, 0600);
    }

    public function clear(): void
    {
        if (file_exists($this->path)) {
            @unlink($this->path);
        }
    }
}
