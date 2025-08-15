<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';
$merchantCode = getenv('TRIPAY_MERCHANT_CODE') ?: null;

$client = new Client($apiKey, $privateKey, false, $merchantCode);

try {
    $payload = [
        'method' => 'BCAVA',
        'merchant_ref' => 'INV'.time(),
        'customer_name' => 'Jane Doe',
    ];

    $resp = $client->openPayment()->create($payload);
    print_r($resp);

    // persist uuid to temp file for other examples
    if (!empty($resp['data']['uuid'])) {
        file_put_contents(__DIR__ . '/.last_open_payment_uuid', $resp['data']['uuid']);
        echo "Wrote OPEN_PAYMENT_UUID to .last_open_payment_uuid\n";
    }
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
