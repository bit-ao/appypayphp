<?php

namespace Bit\AppyPay\Dtos;

/**
 * Item de resposta de GET /references (listagem de referências de pagamento).
 *
 * Difere de {@see ReferenceDto} (esse representa apenas o sub-objecto
 * `reference` retornado em respostas de cobrança/charge).
 */
class ListedReferenceDto
{
    public int     $id              = 0;
    public string  $entity          = '';
    public string  $referenceNumber = '';
    public string  $currency        = 'AOA';
    public ?float  $amount          = null;
    public ?float  $minAmount       = null;
    public ?float  $maxAmount       = null;
    public string  $startDate       = '';
    public string  $expirationDate  = '';
    public bool    $isActive        = true;
    public string  $createdBy       = '';
    public string  $updatedBy       = '';
    public string  $createdDate     = '';
    public string  $updatedDate     = '';

    public static function fromArray(array $data): self
    {
        $dto                  = new self();
        $dto->id              = (int)    ($data['id']              ?? 0);
        $dto->entity          = (string) ($data['entity']          ?? '');
        $dto->referenceNumber = (string) ($data['referenceNumber'] ?? '');
        $dto->currency        = (string) ($data['currency']        ?? 'AOA');
        $dto->amount          = isset($data['amount'])    ? (float) $data['amount']    : null;
        $dto->minAmount       = isset($data['minAmount']) ? (float) $data['minAmount'] : null;
        $dto->maxAmount       = isset($data['maxAmount']) ? (float) $data['maxAmount'] : null;
        $dto->startDate       = (string) ($data['startDate']      ?? '');
        $dto->expirationDate  = (string) ($data['expirationDate'] ?? '');
        $dto->isActive        = (bool)   ($data['isActive']       ?? true);
        $dto->createdBy       = (string) ($data['createdBy']      ?? '');
        $dto->updatedBy       = (string) ($data['updatedBy']      ?? '');
        $dto->createdDate     = (string) ($data['createdDate']    ?? '');
        $dto->updatedDate     = (string) ($data['updatedDate']    ?? '');
        return $dto;
    }
}
