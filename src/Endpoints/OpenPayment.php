<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;
use AndiSiahaan\Tripay\Support\Helper;

class OpenPayment
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
     * Create open payment
     * Required: method, merchant_ref, customer_name
     * signature will be computed if not provided using merchantCode+method+merchantRef
     *
     * @param array $data
     * @return array
     * @throws TripayException
     */
    public function create(array $data): array
    {
        $required = ['method', 'merchant_ref', 'customer_name'];
        foreach ($required as $r) {
            if (empty($data[$r])) {
                throw new TripayException('Parameter "' . $r . '" is required');
            }
        }

        if (empty($data['signature'])) {
            if (empty($this->merchantCode)) {
                throw new TripayException('Merchant code is required to compute signature');
            }
            $data['signature'] = Helper::makeOpenPaymentSignature($this->merchantCode, $this->privateKey, $data);
        }

        $options = [
            'form_params' => $data,
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded'
            ]
        ];

        try {
            $response = $this->http->request('POST', 'open-payment/create', $options);
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

    /**
     * GET /open-payment/{uuid}/detail
     * @param string $uuid
     * @return array
     * @throws TripayException
     */
    public function detail(string $uuid): array
    {
        if (empty($uuid)) {
            throw new TripayException('UUID is required');
        }

    $path = 'open-payment/' . ltrim($uuid, '/') . '/detail';

        try {
            $response = $this->http->request('GET', $path);
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

    /**
     * GET /open-payment/{uuid}/transactions
     * @param string $uuid
     * @return array
     * @throws TripayException
     */
    public function transactions(string $uuid): array
    {
        if (empty($uuid)) {
            throw new TripayException('UUID is required');
        }

    $path = 'open-payment/' . ltrim($uuid, '/') . '/transactions';

        try {
            $response = $this->http->request('GET', $path);
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
