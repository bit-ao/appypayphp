<?php

namespace Bit\AppyPay\Dtos;

class RegisterReferenceDto
{
    public string $paymentMethod = '';

    /** @var RegisterReferenceItemDto[] */
    public array $references = [];

    public ?string $createdBy = null;

    public function toArray(): array
    {
        $arr = [
            'paymentMethod' => $this->paymentMethod,
            'references'    => array_map(
                static fn (RegisterReferenceItemDto $r) => $r->toArray(),
                $this->references,
            ),
        ];
        if ($this->createdBy !== null) $arr['createdBy'] = $this->createdBy;
        return $arr;
    }
}
