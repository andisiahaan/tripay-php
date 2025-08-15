<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';
$merchantCode = getenv('TRIPAY_MERCHANT_CODE') ?: null;

$client = new Client($apiKey, $privateKey, false, $merchantCode);

try {
    $uuid = getenv('OPEN_PAYMENT_UUID') ?: null;
    if (!$uuid && file_exists(__DIR__ . '/.last_open_payment_uuid')) {
        $uuid = trim(file_get_contents(__DIR__ . '/.last_open_payment_uuid')) ?: null;
    }

    if (!$uuid) {
        throw new \Exception('No OPEN_PAYMENT_UUID found. Set OPEN_PAYMENT_UUID or run example_open_payment_create.php first.');
    }

    $resp = $client->openPayment()->detail($uuid);
    print_r($resp);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
