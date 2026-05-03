<?php

namespace Bit\AppyPay\Dtos;

class ListChargesQueryDto
{
    public ?float  $amountFrom            = null;
    public ?float  $amountTo              = null;
    public ?string $currency              = null;
    public ?string $dateFrom              = null;  // ISO 8601
    public ?string $dateTo                = null;  // ISO 8601
    public ?string $disputes              = null;
    public ?int    $limit                 = null;
    public ?string $merchantTransactionId = null;
    public ?int    $skip                  = null;
    public ?string $type                  = null;
    public ?string $culture               = null;

    public function toQueryArray(): array
    {
        $q = [];
        if ($this->amountFrom            !== null) $q['amountFrom']            = $this->amountFrom;
        if ($this->amountTo              !== null) $q['amountTo']              = $this->amountTo;
        if ($this->currency              !== null) $q['currency']              = $this->currency;
        if ($this->dateFrom              !== null) $q['dateFrom']              = $this->dateFrom;
        if ($this->dateTo                !== null) $q['dateTo']                = $this->dateTo;
        if ($this->disputes              !== null) $q['disputes']              = $this->disputes;
        if ($this->limit                 !== null) $q['limit']                 = $this->limit;
        if ($this->merchantTransactionId !== null) $q['merchantTransactionId'] = $this->merchantTransactionId;
        if ($this->skip                  !== null) $q['skip']                  = $this->skip;
        if ($this->type                  !== null) $q['type']                  = $this->type;
        if ($this->culture               !== null) $q['culture']               = $this->culture;
        return $q;
    }
}
