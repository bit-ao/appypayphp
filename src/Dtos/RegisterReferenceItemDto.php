<?php

namespace Bit\AppyPay\Dtos;

class RegisterReferenceItemDto
{
    public string $referenceNumber = '';
    public string $currency        = 'AOA';

    /** @var RegisterReferenceAmountDto[]|null */
    public ?array $amounts = null;

    public ?float  $minAmount      = null;
    public ?float  $maxAmount      = null;
    public ?string $startDate      = null;  // ISO 8601
    public ?string $expirationDate = null;  // ISO 8601

    public function toArray(): array
    {
        $arr = [
            'referenceNumber' => $this->referenceNumber,
            'currency'        => $this->currency,
        ];

        if ($this->amounts !== null) {
            $arr['amounts'] = array_map(
                static fn (RegisterReferenceAmountDto $a) => $a->toArray(),
                $this->amounts,
            );
        }
        if ($this->minAmount      !== null) $arr['minAmount']      = $this->minAmount;
        if ($this->maxAmount      !== null) $arr['maxAmount']      = $this->maxAmount;
        if ($this->startDate      !== null) $arr['startDate']      = $this->startDate;
        if ($this->expirationDate !== null) $arr['expirationDate'] = $this->expirationDate;

        return $arr;
    }
}
