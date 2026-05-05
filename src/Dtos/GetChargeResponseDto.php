<?php

namespace Bit\AppyPay\Dtos;

class GetChargeResponseDto
{
    public ?PaymentDto $payment = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        if (isset($data['payment']) && is_array($data['payment'])) {
            $dto->payment = PaymentDto::fromArray($data['payment']);
        }
        return $dto;
    }
}
