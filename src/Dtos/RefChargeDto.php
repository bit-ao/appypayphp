<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\Enums\PaymentMethod;

class RefChargeDto extends BaseChargeDto
{
    public PaymentMethod $paymentMethod = PaymentMethod::ref;

    public function __construct()
    {
        $this->paymentInfo = new PaymentInfoRefDto();
    }
}
