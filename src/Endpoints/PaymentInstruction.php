<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;
use AndiSiahaan\Tripay\Utils\Signer;

class PaymentInstruction
{
    private GuzzleClient $http;
    private string $privateKey;

    public function __construct(GuzzleClient $http, string $privateKey)
    {
        $this->http = $http;
        $this->privateKey = $privateKey;
    }

    /**
     * GET /payment/instruction
     * Parameters:
     * - code (string) REQUIRED
     * - pay_code (string) OPTIONAL
     * - amount (int) OPTIONAL
     * - allow_html (int) OPTIONAL (0|1)
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

        // Sign payload as Tripay expects (placeholder)
        $signature = Signer::sign($params, $this->privateKey);

        $options = [
            'headers' => [
                'Signature' => $signature,
            ],
            'query' => $params,
        ];

        try {
            $response = $this->http->request('GET', 'payment/instruction', $options);
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
