<?php

namespace Bit\AppyPay\Exception;

class AppyPayErrorHandler
{
    public static function handle(\Throwable $err): array
    {
        $original = $err instanceof AppyPayException ? $err->original : null;
        $message  = 'Unknown error';
        $code     = 500;

        if (is_array($original)) {
            if (isset($original['responseStatus'])) {
                $message = $original['responseStatus']['message'] ?? $message;
                $code    = $original['responseStatus']['code']    ?? $code;
            } elseif (isset($original['error'], $original['error_description'])) {
                $message = "{$original['error']}: {$original['error_description']}";
            }
        }

        return [
            'success'  => false,
            'code'     => $code,
            'message'  => $message,
            'original' => $original,
        ];
    }
}
