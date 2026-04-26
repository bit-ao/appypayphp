<?php

namespace Bit\AppyPay;

class AccessToken
{
    public function __construct(
        public readonly string  $tokenType,
        public readonly string  $accessToken,
        public readonly int     $expiresOn,
        public readonly ?string $resource = null,
    ) {}

    public static function fromResponse(array $data): self
    {
        return new self(
            tokenType:   $data['token_type'] ?? '',
            accessToken: $data['access_token'] ?? '',
            expiresOn:   isset($data['expires_on'])
                ? (int) $data['expires_on']
                : (time() + (int) ($data['expires_in'] ?? 3600)),
            resource:    $data['resource'] ?? null,
        );
    }

    public function isExpired(): bool
    {
        return time() >= $this->expiresOn;
    }
}
