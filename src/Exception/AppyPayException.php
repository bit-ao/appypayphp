<?php

namespace Bit\AppyPay\Exception;

class AppyPayException extends \RuntimeException
{
    public bool  $isAppyPayError = true;
    public string $errorCode;
    public mixed  $original;

    public function __construct(string $message, string $errorCode = '', mixed $original = null)
    {
        parent::__construct($message);
        $this->errorCode = $errorCode;
        $this->original  = $original;
    }
}
