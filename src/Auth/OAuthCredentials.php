<?php

namespace Bit\AppyPay\Auth;

class OAuthCredentials
{
    public function __construct(
        public readonly string $clientId,
        public readonly string $clientSecret,
        public readonly string $resource,
    ) {}
}
