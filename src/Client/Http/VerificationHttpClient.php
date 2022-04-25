<?php

declare(strict_types=1);

namespace App\Client\Http;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\ResponseInterface;

class VerificationHttpClient extends AbstractHttpClient
{
    public function requestNotificationTemplate(string $slug, array $variables): ResponseInterface
    {
        return $this->httpClient->request(
            Request::METHOD_POST,
            '/templates/render',
            [
                'json' => [
                    'slug' => $slug,
                    'variables' => $variables,
                ],
            ],
        );
    }
}
