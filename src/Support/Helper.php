<?php

namespace AndiSiahaan\Tripay\Support;

use AndiSiahaan\Tripay\Client;
use Exception;

class Helper
{
    public static function formatAmount($amount): int
    {
        if (!is_numeric($amount)) {
            throw new Exception('Amount must be numeric value');
        }

        return (int) number_format($amount, 0, '', '');
    }

    public static function makeTimestamp(string $value): int
    {
        if (!preg_match('/^[0-9]+[\s][A-Z]+$/is', $value)) {
            throw new Exception("Value must be in '[value] [unit]' format: i.e: 1 DAY");
        }

        [$v, $unit] = explode(' ', $value);
        $v = (int) $v;
        if ($v === 0) {
            throw new Exception('Value must be greater than 0');
        }

        $unit = strtoupper($unit);
        switch ($unit) {
            case 'SECOND': $sec = $v; break;
            case 'MINUTE': $sec = $v * 60; break;
            case 'HOUR':   $sec = $v * 60 * 60; break;
            case 'DAY':    $sec = $v * 24 * 60 * 60; break;
            default: throw new Exception('Unsupported time unit');
        }

        return (int) (time() + $sec);
    }

    public static function makeSignature(?string $merchantCode, string $privateKey, array $payloads): string
    {
        $merchantRef = $payloads['merchant_ref'] ?? '';
        $amount = isset($payloads['amount']) ? self::formatAmount($payloads['amount']) : 0;

        return hash_hmac('sha256', ($merchantCode ?? '') . $merchantRef . $amount, $privateKey);
    }

    public static function makeOpenPaymentSignature(?string $merchantCode, string $privateKey, array $payloads): string
    {
        $merchantRef = $payloads['merchant_ref'] ?? '';
        $method = $payloads['method'] ?? '';

        return hash_hmac('sha256', ($merchantCode ?? '') . $method . $merchantRef, $privateKey);
    }
}
