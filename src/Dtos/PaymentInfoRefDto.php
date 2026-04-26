<?php

namespace Bit\AppyPay\Dtos;

class PaymentInfoRefDto
{
    public string  $referenceNumber = '';
    public ?string $dueDate         = null;

    public function toArray(): array
    {
        $arr = ['referenceNumber' => $this->referenceNumber];
        if ($this->dueDate !== null) {
            $arr['dueDate'] = $this->dueDate;
        }
        return $arr;
    }
}
