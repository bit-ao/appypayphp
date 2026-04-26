<?php

namespace Bit\AppyPay\Dtos;

class PaymentWebHookDto
{
    public string  $id                    = '';
    public string  $merchantTransactionId = '';
    public float   $amount                = 0.0;
    public ?array  $options               = null;
    public ?array  $reference             = null;
    public ?array  $eletronicReceipt      = null;
    public array   $responseStatus        = [];

    public static function fromArray(array $data): self
    {
        $dto                        = new self();
        $dto->id                    = $data['id']                    ?? '';
        $dto->merchantTransactionId = $data['merchantTransactionId'] ?? '';
        $dto->amount                = $data['amount']                ?? 0.0;
        $dto->options               = $data['options']               ?? null;
        $dto->reference             = $data['reference']             ?? null;
        $dto->eletronicReceipt      = $data['eletronicReceipt']      ?? null;
        $dto->responseStatus        = $data['responseStatus']        ?? [];
        return $dto;
    }
}
