<?php

namespace Bit\AppyPay\Enums;

enum PaymentMethod: string
{
    case ref      = 'ref';
    case express  = 'express';
    case aexpress = 'aexpress';
    case qr       = 'qr';
}
