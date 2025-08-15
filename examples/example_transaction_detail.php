<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';

$client = new Client($apiKey, $privateKey, false);

try {
    $reference = getenv('REFERENCE') ?: null;
    if (!$reference) {
        // use latest transaction from list
        $list = $client->transactions()->list(['page' => 1, 'per_page' => 1]);
        $reference = $list['data'][0]['reference'] ?? null;
    }

    if (!$reference) {
        throw new \Exception('No transaction reference available. Set REFERENCE env var or ensure you have transactions in sandbox.');
    }

    $resp = $client->transactions()->detail(['reference' => $reference]);
    print_r($resp);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
