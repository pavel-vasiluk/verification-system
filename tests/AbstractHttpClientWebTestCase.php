<?php

declare(strict_types=1);

namespace App\Tests;

use JsonException;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractHttpClientWebTestCase extends AbstractWebTestCase
{
    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = self::createClient();
        parent::setUp();
    }

    protected function sendGetRequest(string $uri, array $parameters = []): void
    {
        $this->client->request(Request::METHOD_GET, $uri, $parameters);
    }

    /**
     * @throws JsonException
     */
    protected function sendPostRequest(string $uri, array $payload = [], array $parameters = [], array $headers = []): void
    {
        $this->client->request(
            Request::METHOD_POST,
            $uri,
            $parameters,
            [],
            $headers,
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    protected function sendPutRequest(string $uri, array $payload = [], array $parameters = [], array $headers = []): void
    {
        $request = $this->client->request(
            Request::METHOD_PUT,
            $uri,
            $parameters,
            [],
            $headers,
            json_encode($payload, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    protected function assertResponseHasJson(array $json): void
    {
        self::assertSame(
            $json,
            json_decode($this->client->getResponse()->getContent(), true, 512, JSON_THROW_ON_ERROR)
        );
    }

    /**
     * @throws JsonException
     */
    protected function assertResponseHasContent(string $content): void
    {
        self::assertSame(
            $content,
            $this->client->getResponse()->getContent()
        );
    }
}
