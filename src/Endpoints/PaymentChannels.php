<?php

namespace AndiSiahaan\Tripay\Endpoints;

use GuzzleHttp\Client as GuzzleClient;
use AndiSiahaan\Tripay\Exceptions\TripayException;

class PaymentChannels
{
    private GuzzleClient $http;

    public function __construct(GuzzleClient $http)
    {
        $this->http = $http;
    }

    /**
     * GET /merchant/payment-channel
     * No parameters expected. Returns list of channels.
     *
     * @return array
     * @throws TripayException
     */
    public function list(): array
    {
        try {
            $response = $this->http->request('GET', 'merchant/payment-channel');
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
