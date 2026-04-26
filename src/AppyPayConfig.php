<?php

namespace Bit\AppyPay;

class AppyPayConfig
{
    public ?string              $baseUrl      = null;
    public ?string              $authUrl      = null;
    public ?string              $version      = null;
    public ?string              $clientId     = null;
    public ?string              $clientSecret = null;
    public ?string              $resource     = null;
    public ?string              $posCode      = null;
    public ?PaymentMethodsConfig $methods      = null;
}
