<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;

class Transactions
{
    private GuzzleClient $http;

    public function __construct(GuzzleClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /merchant/transactions
     * Optional params: page (int), per_page (int), start_date, end_date, merchant_ref, status, etc.
     *
     * @param array $params
     * @return array
     * @throws TripayException
     */
    public function list(array $params = []): array
    {
        // Set defaults
        $params = array_merge(['page' => 1, 'per_page' => 25], $params);

        $options = [
            'query' => $params,
        ];

        try {
            $response = $this->http->request('GET', 'merchant/transactions', $options);
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
     * GET /transaction/detail
     * Required param: reference
     *
     * @param array $params
     * @return array
     * @throws TripayException
     */
    public function detail(array $params = []): array
    {
        if (empty($params['reference'])) {
            throw new TripayException('Parameter "reference" is required');
        }

        $options = ['query' => $params];

        try {
            $response = $this->http->request('GET', 'transaction/detail', $options);
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
     * GET /transaction/check-status
     * Required param: reference
     *
     * @param array $params
     * @return array
     * @throws TripayException
     */
    public function status(array $params = []): array
    {
        if (empty($params['reference'])) {
            throw new TripayException('Parameter "reference" is required');
        }

        $options = ['query' => $params];

        try {
            $response = $this->http->request('GET', 'transaction/check-status', $options);
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
