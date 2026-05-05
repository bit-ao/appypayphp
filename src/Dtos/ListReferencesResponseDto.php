<?php

namespace Bit\AppyPay\Dtos;

class ListReferencesResponseDto
{
    /** @var ListedReferenceDto[] */
    public array $references = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        foreach (($data['references'] ?? []) as $r) {
            $dto->references[] = ListedReferenceDto::fromArray($r);
        }
        return $dto;
    }
}
