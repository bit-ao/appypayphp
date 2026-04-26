<?php

namespace Bit\AppyPay\Dtos;

class SourceDetailsDto
{
    public int    $attempt = 0;
    public string $type    = '';
    public string $code    = '';
    public string $message = '';

    public static function fromArray(array $data): self
    {
        $dto          = new self();
        $dto->attempt = $data['attempt'] ?? 0;
        $dto->type    = $data['type']    ?? '';
        $dto->code    = $data['code']    ?? '';
        $dto->message = $data['message'] ?? '';
        return $dto;
    }
}
