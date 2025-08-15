<?php

namespace AndiSiahaan\Tripay;

use AndiSiahaan\Tripay\Exceptions\TripayException;
use AndiSiahaan\Tripay\Utils\Signer;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class Client
{
    private string $apiKey;
    private string $privateKey;
    private string $baseUrl;
    private array $defaultHeaders = [];
    private GuzzleClient $http;
    private array $debugs = [];
    private ?string $merchantCode = null;

    public function __construct(string $apiKey, string $privateKey, bool $isProduction = false, ?string $merchantCode = null)
    {
        $this->apiKey = $apiKey;
        $this->privateKey = $privateKey;
        $this->merchantCode = $merchantCode;
    $this->baseUrl = $isProduction ? 'https://tripay.co.id/api/' : 'https://tripay.co.id/api-sandbox/';

        $this->defaultHeaders = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'AndiSiahaan/tripay-php (+https://github.com/AndiSiahaan/tripay-php)'
        ];

        $this->debugs = [
            'request' => null,
            'response' => null,
        ];

        $this->http = new GuzzleClient([
            'base_uri' => $this->baseUrl,
            'headers' => $this->defaultHeaders,
            'http_errors' => false, // we'll handle errors ourselves
            'timeout' => 10,
            'on_stats' => function (\GuzzleHttp\TransferStats $stats) {
                $hasResponse = $stats->hasResponse();
                $this->debugs = array_merge($this->debugs, [
                    'request' => [
                        'url' => (string) $stats->getEffectiveUri(),
                        'method' => $stats->getRequest()->getMethod(),
                        'headers' => (array) $stats->getRequest()->getHeaders(),
                        'body' => (string) $stats->getRequest()->getBody(),
                    ],
                    'response' => [
                        'status' => (int) ($hasResponse ? $stats->getResponse()->getStatusCode() : 0),
                        'headers' => (array) ($hasResponse ? $stats->getResponse()->getHeaders() : []),
                        'body' => (string) ($hasResponse ? $stats->getResponse()->getBody() : ''),
                    ],
                ]);
            }
        ]);
    }

    /**
     * Set merchant code (optional)
     */
    public function setMerchantCode(string $merchantCode): void
    {
        $this->merchantCode = $merchantCode;
    }

    /**
     * Get merchant code if set
     */
    public function getMerchantCode(): ?string
    {
        return $this->merchantCode;
    }

    /**
     * Get debug data collected from last request
     */
    public function getDebugs(): array
    {
        return $this->debugs ?? [];
    }

    /**
     * Expose the underlying Guzzle client for endpoint classes or tests.
     */
    public function httpClient(): GuzzleClient
    {
        return $this->http;
    }

    /**
     * Get private key
     */
    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    /**
     * Factory for PaymentInstruction endpoint helper
     */
    public function paymentInstruction(): Endpoints\PaymentInstruction
    {
        return new Endpoints\PaymentInstruction($this->http, $this->privateKey);
    }

    /**
     * Factory for Payment Channels endpoint
     */
    public function paymentChannels(): Endpoints\PaymentChannels
    {
        return new Endpoints\PaymentChannels($this->http);
    }

    /**
     * Factory for Fee Calculator endpoint
     */
    public function feeCalculator(): Endpoints\FeeCalculator
    {
        return new Endpoints\FeeCalculator($this->http);
    }

    /**
     * Factory for Transactions listing endpoint
     */
    public function transactions(): Endpoints\Transactions
    {
        return new Endpoints\Transactions($this->http);
    }

    /**
     * Factory for Transaction create endpoint
     */
    public function transaction(): Endpoints\Transaction
    {
        return new Endpoints\Transaction($this->http, $this->merchantCode, $this->privateKey);
    }

    /**
     * Factory for Open Payment endpoint
     */
    public function openPayment(): Endpoints\OpenPayment
    {
        return new Endpoints\OpenPayment($this->http, $this->merchantCode, $this->privateKey);
    }

    /**
     * Generic request helper using Guzzle
     * @param string $method
     * @param string $path
     * @param array|null $body
     * @return array Decoded JSON response
     * @throws TripayException
     */
    public function request(string $method, string $path, ?array $body = null): array
    {
        $method = strtoupper($method);
        $payload = $body ?? [];
        $signature = Signer::sign($payload, $this->privateKey);

        $headers = $this->defaultHeaders;
        $headers['Signature'] = $signature;

        $options = [
            'headers' => $headers,
        ];

        if (!empty($payload)) {
            // For GET requests, put payload in query
            if ($method === 'GET') {
                $options['query'] = $payload;
            } else {
                $options['json'] = $payload;
            }
        }

        try {
            $response = $this->http->request($method, ltrim($path, '/'), $options);
        } catch (GuzzleException $e) {
            throw new TripayException('HTTP request failed: ' . $e->getMessage());
        }

        $code = $response->getStatusCode();
        $bodyContents = (string) $response->getBody();

        $decoded = json_decode($bodyContents, true);
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
     * Example wrapper: create transaction
     */
    public function createTransaction(array $data): array
    {
    return $this->transaction()->create($data);
    }

    // Add more wrappers (getTransaction, listPayments, etc.) as needed
}
