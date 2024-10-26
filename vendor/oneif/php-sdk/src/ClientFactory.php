<?php

declare(strict_types=1);

namespace OneIf\Payment;

use GuzzleHttp\Client as Guzzle;
use GuzzleHttp\RequestOptions;

class ClientFactory
{
    const URI = "https://api.1if.io";
    const STAGE_URI = "https://api.1if.tech";

    public static function build(string $apiKey, string $webhookSecret, bool $isSandbox = false): Client
    {
        $uri = self::URI;
        if ($isSandbox) {
            $uri = self::STAGE_URI;
        }

        $httpClient = new Guzzle([
            'base_uri' => $uri,
            RequestOptions::HEADERS => [
                'X-Api-Key' => $apiKey
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return new ApiClient($httpClient, $webhookSecret);
    }
}
