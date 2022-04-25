<?php

declare(strict_types=1);

namespace App\Client\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class AbstractHttpClient
{
    protected HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient, string $baseUri)
    {
        $this->httpClient = $httpClient->withOptions([
            'base_uri' => $baseUri,
        ]);
    }
}
