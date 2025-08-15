<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;
use AndiSiahaan\Tripay\Support\Helper;

class Transaction
{
    private GuzzleClient $http;
    private ?string $merchantCode;
    private string $privateKey;

    public function __construct(GuzzleClient $http, ?string $merchantCode, string $privateKey)
    {
        $this->http = $http;
        $this->merchantCode = $merchantCode;
        $this->privateKey = $privateKey;
    }

    /**
     * Create transaction
     * Required fields: method, merchant_ref, amount, customer_name, customer_email, customer_phone
     * order_items is optional but recommended
     * signature will be computed if not provided using merchantCode+merchantRef+amount
     *
     * @param array $data
     * @return array
     * @throws TripayException
     */
    public function create(array $data): array
    {
        $required = ['method', 'merchant_ref', 'customer_name', 'customer_email', 'customer_phone'];
        foreach ($required as $r) {
            if (empty($data[$r])) {
                throw new TripayException('Parameter "' . $r . '" is required');
            }
        }

        // If order_items present and amount missing, compute amount automatically
        if (empty($data['amount']) && !empty($data['order_items']) && is_array($data['order_items'])) {
            $amount = 0;
            foreach ($data['order_items'] as $it) {
                $price = $it['price'] ?? 0;
                $qty = $it['quantity'] ?? 1;
                $amount += $price * $qty;
            }
            $data['amount'] = $amount;
        }

        if (empty($data['amount'])) {
            throw new TripayException('Parameter "amount" is required');
        }

        if (empty($data['signature'])) {
            if (empty($this->merchantCode)) {
                throw new TripayException('Merchant code is required to compute signature');
            }
            $data['signature'] = Helper::makeSignature($this->merchantCode, $this->privateKey, $data);
        }

        // Tripay expects form-encoded body
        $options = [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];

        try {
            $response = $this->http->request('POST', 'transaction/create', $options);
        } catch (\Exception $e) {
            throw new TripayException('HTTP request failed: ' . $e->getMessage());
        }

        $code = $response->getStatusCode();
        $body = (string) $response->getBody();
        $decoded = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new TripayException('Invalid JSON response: ' . json_last_error_msg());
        }

        if ($code < 200 || $code >= 300) {
            $message = $decoded['message'] ?? 'HTTP error ' . $code;
            throw new TripayException($message, $code);
        }

        return $decoded;
    }
}
