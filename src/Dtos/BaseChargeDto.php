<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\Enums\PaymentMethod;

class BaseChargeDto
{
    public float   $amount                = 0.0;
    public ?string $currency              = null;
    public ?string $description           = null;
    public string  $merchantTransactionId = '';
    public ?array  $options               = null;
    public ?NotifyDto $notify             = null;
    public PaymentMethod $paymentMethod   = PaymentMethod::express;
    public PaymentInfoGpoDto|PaymentInfoRefDto|PaymentInfoEtpaDto|null $paymentInfo = null;
}
