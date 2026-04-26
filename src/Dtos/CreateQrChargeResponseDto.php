<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\ChargeResponseInterface;

class CreateQrChargeResponseDto implements ChargeResponseInterface
{
    public string            $id             = '';
    public string            $qrCodeArr      = '';
    public ResponseStatusDto $responseStatus;

    public static function fromArray(array $data): self
    {
        // API wraps the payload under a "data" key
        $inner = $data['data'] ?? $data;

        $dto               = new self();
        $dto->id           = $inner['id']        ?? '';
        $dto->qrCodeArr    = $inner['qrCodeArr'] ?? '';

        $dto->responseStatus = isset($inner['responseStatus'])
            ? ResponseStatusDto::fromArray($inner['responseStatus'])
            : new ResponseStatusDto();

        return $dto;
    }
}
