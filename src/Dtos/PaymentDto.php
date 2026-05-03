<?php

namespace Bit\AppyPay\Dtos;

class PaymentDto
{
    public string  $id                    = '';
    public ?string $merchantTransactionId = null;
    public ?string $type                  = null;
    public ?string $operation             = null;
    public float   $amount                = 0.0;
    public ?string $currency              = null;
    public ?string $status                = null;  // Requested|Pending|Success|Failed
    public ?string $description           = null;
    public ?bool   $disputes              = null;
    public ?float  $applicationFeeAmount  = null;
    public ?string $paymentMethod         = null;
    public ?string $createdDate           = null;
    public ?string $updatedDate           = null;
    public ?array  $options               = null;
    public ?ReferenceDto $reference       = null;

    public static function fromArray(array $data): self
    {
        $dto                        = new self();
        $dto->id                    = (string) ($data['id'] ?? '');
        $dto->merchantTransactionId = $data['merchantTransactionId'] ?? null;
        $dto->type                  = $data['type'] ?? null;
        $dto->operation             = $data['operation'] ?? null;
        $dto->amount                = (float) ($data['amount'] ?? 0);
        $dto->currency              = $data['currency'] ?? null;
        $dto->status                = $data['status'] ?? null;
        $dto->description           = $data['description'] ?? null;
        $dto->disputes              = isset($data['disputes']) ? (bool) $data['disputes'] : null;
        $dto->applicationFeeAmount  = isset($data['applicationFeeAmount']) ? (float) $data['applicationFeeAmount'] : null;
        $dto->paymentMethod         = $data['paymentMethod'] ?? null;
        $dto->createdDate           = $data['createdDate'] ?? null;
        $dto->updatedDate           = $data['updatedDate'] ?? null;
        $dto->options               = is_array($data['options'] ?? null) ? $data['options'] : null;
        $dto->reference             = is_array($data['reference'] ?? null)
            ? ReferenceDto::fromArray($data['reference'])
            : null;
        return $dto;
    }
}
