<?php

namespace Bit\AppyPay;

interface TokenStoragePort
{
    public function get(): ?AccessToken;
    public function set(AccessToken $token): void;
    public function clear(): void;
}
