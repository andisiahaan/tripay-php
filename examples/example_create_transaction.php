<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;
use AndiSiahaan\Tripay\Support\Helper;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';
$merchantCode = getenv('TRIPAY_MERCHANT_CODE') ?: null;

$client = new Client($apiKey, $privateKey, false, $merchantCode);

try {
    $payload = [
        'method' => 'BRIVA',
        'merchant_ref' => 'ORDER-' . time(),
        'amount' => 10000,
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '081234567890',
        'order_items' => [
            ['sku' => 'FB-06', 'name' => 'Produk 1', 'price' => 5000, 'quantity' => 2]
        ],
        'return_url' => 'https://example.com/redirect',
    'expired_time' => Helper::makeTimestamp('1 DAY'),
    ];

    $resp = $client->transaction()->create($payload);
    print_r($resp);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
