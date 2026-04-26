<?php

namespace Bit\AppyPay\Storage;

use Bit\AppyPay\AccessToken;
use Bit\AppyPay\TokenStoragePort;

class MemoryTokenStorage implements TokenStoragePort
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
