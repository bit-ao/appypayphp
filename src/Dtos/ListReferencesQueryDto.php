<?php

namespace Bit\AppyPay\Dtos;

class ListReferencesQueryDto
{
    public ?float  $amountFrom = null;
    public ?float  $amountTo   = null;
    public ?string $dateFrom   = null;  // ISO 8601
    public ?string $dateTo     = null;  // ISO 8601
    public ?int    $limit      = null;
    public ?int    $skip       = null;
    public ?string $culture    = null;

    public function toQueryArray(): array
    {
        $q = [];
        if ($this->amountFrom !== null) $q['amountFrom'] = $this->amountFrom;
        if ($this->amountTo   !== null) $q['amountTo']   = $this->amountTo;
        if ($this->dateFrom   !== null) $q['dateFrom']   = $this->dateFrom;
        if ($this->dateTo     !== null) $q['dateTo']     = $this->dateTo;
        if ($this->limit      !== null) $q['limit']      = $this->limit;
        if ($this->skip       !== null) $q['skip']       = $this->skip;
        if ($this->culture    !== null) $q['culture']    = $this->culture;
        return $q;
    }
}
