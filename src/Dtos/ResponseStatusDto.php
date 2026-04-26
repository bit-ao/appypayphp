<?php

namespace Bit\AppyPay\Dtos;

class ResponseStatusDto
{
    public bool             $successful   = false;
    public string           $status       = '';
    public int              $code         = 0;
    public string           $message      = '';
    public string           $source       = '';
    public ?SourceDetailsDto $sourceDetails = null;
    public int              $attempt      = 0;
    public string           $type         = '';

    public static function fromArray(array $data): self
    {
        $dto               = new self();
        $dto->successful   = $data['successful'] ?? false;
        $dto->status       = $data['status']     ?? '';
        $dto->code         = $data['code']        ?? 0;
        $dto->message      = $data['message']    ?? '';
        $dto->source       = $data['source']     ?? '';
        $dto->attempt      = $data['attempt']    ?? 0;
        $dto->type         = $data['type']       ?? '';

        if (isset($data['sourceDetails'])) {
            $dto->sourceDetails = SourceDetailsDto::fromArray($data['sourceDetails']);
        }

        return $dto;
    }
}
