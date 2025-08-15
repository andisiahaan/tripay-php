<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';

$merchantCode = getenv('TRIPAY_MERCHANT_CODE') ?: null;
$client = new Client($apiKey, $privateKey, false, $merchantCode);

try {
    $payload = [
        'merchant_ref' => 'ORDER-' . time(),
        'amount' => 10000,
        'method' => 'BRIVA',
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '081234567890',
        'order_items' => [
            ['sku' => 'FB-06', 'name' => 'Produk 1', 'price' => 5000, 'quantity' => 2]
        ],
        // other fields per Tripay API
    ];

    $response = $client->createTransaction($payload);
    echo "Response:\n";
    print_r($response);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
