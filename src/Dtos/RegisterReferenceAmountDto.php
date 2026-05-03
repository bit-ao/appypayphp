<?php

namespace Bit\AppyPay\Dtos;

class RegisterReferenceAmountDto
{
    public float   $amount           = 0.0;
    public ?string $descriptionLine1 = null;
    public ?string $descriptionLine2 = null;

    public function toArray(): array
    {
        $arr = ['amount' => $this->amount];
        if ($this->descriptionLine1 !== null) $arr['descriptionLine1'] = $this->descriptionLine1;
        if ($this->descriptionLine2 !== null) $arr['descriptionLine2'] = $this->descriptionLine2;
        return $arr;
    }
}
