<?php

namespace Bit\AppyPay\Dtos;

class TransactionEventDto
{
    public int                $id                    = 0;
    public string             $transactionId         = '';
    public string             $type                  = '';
    public ?string            $providerTransactionId = null;
    public bool               $actionStatus          = false;
    public string             $createdDate           = '';
    public ?ResponseStatusDto $responseStatus        = null;

    public static function fromArray(array $data): self
    {
        $dto                        = new self();
        $dto->id                    = (int)    ($data['id']                    ?? 0);
        $dto->transactionId         = (string) ($data['transactionId']         ?? '');
        $dto->type                  = (string) ($data['type']                  ?? '');
        $dto->providerTransactionId = $data['providerTransactionId'] ?? null;
        $dto->actionStatus          = (bool)   ($data['actionStatus']          ?? false);
        $dto->createdDate           = (string) ($data['createdDate']           ?? '');

        if (isset($data['responseStatus'])) {
            $dto->responseStatus = ResponseStatusDto::fromArray($data['responseStatus']);
        }

        return $dto;
    }
}
