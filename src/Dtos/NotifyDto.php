<?php

namespace Bit\AppyPay\Dtos;

class NotifyDto
{
    public ?string $name              = null;
    public ?string $telephone         = null;
    public ?string $email             = null;
    public ?bool   $smsNotification   = null;
    public ?bool   $emailNotification = null;
}
