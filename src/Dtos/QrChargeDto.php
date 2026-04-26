<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\Enums\PaymentMethod;

class QrChargeDto extends BaseChargeDto
{
    public PaymentMethod $paymentMethod  = PaymentMethod::qr;
    public string        $qrCodeType     = 'SINGLE';
    public ?float            $minAmount      = null;
    public ?int              $maxTransactions = null;
    public ?string           $startDate      = null;
    public ?string           $endDate        = null;

    public function __construct()
    {
        $this->paymentInfo = new PaymentInfoEtpaDto();
    }
}
