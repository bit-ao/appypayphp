<?php

namespace Bit\AppyPay\Dtos;

class RegisterReferenceResultDto
{
    public string $referenceNumber = '';
    public int    $code            = 0;
    public string $message         = '';

    public static function fromArray(array $data): self
    {
        $dto                  = new self();
        $dto->referenceNumber = (string) ($data['referenceNumber'] ?? '');
        $dto->code            = (int)    ($data['code']            ?? 0);
        $dto->message         = (string) ($data['message']         ?? '');
        return $dto;
    }
}
