<?php

namespace Bit\AppyPay\Storage;

use Bit\AppyPay\AccessToken;
use Bit\AppyPay\TokenStoragePort;

/**
 * Stub Redis storage — replace with a real Redis adapter (predis/phpredis) as needed.
 */
class RedisTokenStorage implements TokenStoragePort
{
    private ?AccessToken $token = null;

    public function get(): ?AccessToken
    {
        return $this->token;
    }

    public function set(AccessToken $token): void
    {
        $this->token = $token;
    }

    public function clear(): void
    {
        $this->token = null;
    }
}
