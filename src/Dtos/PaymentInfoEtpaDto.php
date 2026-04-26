<?php

namespace Bit\AppyPay\Dtos;

class PaymentInfoEtpaDto
{
    public string $posCode = '';

    public function toArray(): array
    {
        return ['posCode' => $this->posCode];
    }
}
