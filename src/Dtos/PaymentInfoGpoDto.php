<?php

namespace Bit\AppyPay\Dtos;

class PaymentInfoGpoDto
{
    public string $phoneNumber = '';

    public function toArray(): array
    {
        return ['phoneNumber' => $this->phoneNumber];
    }
}
