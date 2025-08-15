<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';

$client = new Client($apiKey, $privateKey, false);

try {
    $resp = $client->transactions()->list(['page' => 1, 'per_page' => 25]);
    print_r($resp);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
