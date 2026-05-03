<?php

namespace Bit\AppyPay\Dtos;

class ListChargesResponseDto
{
    /** @var PaymentDto[] */
    public array $payments = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        foreach (($data['payments'] ?? []) as $p) {
            $dto->payments[] = PaymentDto::fromArray($p);
        }
        return $dto;
    }
}
