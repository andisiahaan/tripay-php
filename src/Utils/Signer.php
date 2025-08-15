<?php

namespace AndiSiahaan\Tripay\Utils;

class Signer
{
    /**
     * Create a simple HMAC signature of the payload using the private key.
     * Tripay's real signature rules may differ; update as required.
     * @param array $payload
     * @param string $privateKey
     * @return string
     */
    public static function sign(array $payload, string $privateKey): string
    {
        ksort($payload);
        $data = http_build_query($payload);
        return hash_hmac('sha256', $data, $privateKey);
    }
}
