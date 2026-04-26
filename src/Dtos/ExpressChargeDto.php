<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\Enums\PaymentMethod;

class ExpressChargeDto extends BaseChargeDto
{
    public PaymentMethod $paymentMethod = PaymentMethod::express;

    public function __construct()
    {
        $this->paymentInfo = new PaymentInfoGpoDto();
    }
}
