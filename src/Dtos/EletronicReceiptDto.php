<?php

namespace Bit\AppyPay\Dtos;

class EletronicReceiptDto
{
    public string $customerReceipt = '';
    public string $merchantReceipt = '';

    public static function fromArray(array $data): self
    {
        $dto                  = new self();
        $dto->customerReceipt = $data['customerReceipt'] ?? '';
        $dto->merchantReceipt = $data['merchantReceipt'] ?? '';
        return $dto;
    }
}
