<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;

class FeeCalculator
{
    private GuzzleClient $http;

    public function __construct(GuzzleClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /merchant/fee-calculator
     * Required params: code (string), amount (int)
     *
     * @param array $params
     * @return array
     * @throws TripayException
     */
    public function get(array $params = []): array
    {
        if (empty($params['code'])) {
            throw new TripayException('Parameter "code" is required');
        }
        if (!isset($params['amount']) || !is_numeric($params['amount'])) {
            throw new TripayException('Parameter "amount" is required and must be numeric');
        }

        $options = [
            'query' => $params,
        ];

        try {
            $response = $this->http->request('GET', 'merchant/fee-calculator', $options);
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
