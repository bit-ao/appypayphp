<?php

namespace Bit\AppyPay\Dtos;

use Bit\AppyPay\ChargeResponseInterface;

class CreateChargeResponseDto implements ChargeResponseInterface
{
    public string             $id             = '';
    public ResponseStatusDto  $responseStatus;
    public ?ReferenceDto      $reference      = null;
    public ?EletronicReceiptDto $eletronicReceipt = null;

    public static function fromArray(array $data): self
    {
        $dto = new self();
        $dto->id = $data['id'] ?? '';

        $dto->responseStatus = isset($data['responseStatus'])
            ? ResponseStatusDto::fromArray($data['responseStatus'])
            : new ResponseStatusDto();

        if (isset($data['reference'])) {
            $dto->reference = ReferenceDto::fromArray($data['reference']);
        }

        if (isset($data['eletronicReceipt'])) {
            $dto->eletronicReceipt = EletronicReceiptDto::fromArray($data['eletronicReceipt']);
        }

        return $dto;
    }
}
