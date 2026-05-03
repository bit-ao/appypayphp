<?php

namespace Bit\AppyPay\Dtos;

class RegisterReferenceResponseDto
{
    /** @var RegisterReferenceResultDto[] */
    public array $references = [];

    public static function fromArray(array $data): self
    {
        $dto = new self();
        foreach (($data['references'] ?? []) as $r) {
            $dto->references[] = RegisterReferenceResultDto::fromArray($r);
        }
        return $dto;
    }
}
