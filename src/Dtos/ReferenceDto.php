<?php

namespace Bit\AppyPay\Dtos;

class ReferenceDto
{
    public string $referenceNumber = '';
    public string $dueDate         = '';
    public string $entity          = '';

    public static function fromArray(array $data): self
    {
        $dto                  = new self();
        $dto->referenceNumber = $data['referenceNumber'] ?? '';
        $dto->dueDate         = $data['dueDate']         ?? '';
        $dto->entity          = $data['entity']          ?? '';
        return $dto;
    }
}
