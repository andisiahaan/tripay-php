<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AndiSiahaan\Tripay\Client;

$apiKey = getenv('TRIPAY_API_KEY') ?: 'your_api_key_here';
$privateKey = getenv('TRIPAY_PRIVATE_KEY') ?: 'your_private_key_here';

$client = new Client($apiKey, $privateKey, false);

try {
    $channels = $client->paymentChannels()->list();
    print_r($channels);
} catch (\Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
}
