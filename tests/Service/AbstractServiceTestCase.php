<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\DTO\Request\VerificationUserInfoDTO;
use App\Component\Request\AbstractUserInfoAwareRequest;
use App\Tests\AbstractWebTestCase;
use JsonException;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

abstract class AbstractServiceTestCase extends AbstractWebTestCase
{
    protected const REQUEST_USER_INFO = [
        'clientIp' => '0.0.0.0',
        'userAgent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    ];

    /**
     * @throws JsonException
     */
    protected function prepareHttpRequestWithBody(array $requestBody, array $attributes = []): Request
    {
        $request = new Request();
        $request->initialize([], [], $attributes, [], [], [], json_encode($requestBody, JSON_THROW_ON_ERROR));

        return $request;
    }

    protected function setRequestUserInfo(AbstractUserInfoAwareRequest $request, array $userInfo = []): void
    {
        $reflectionClass = new ReflectionClass($request);
        $reflectionProperty = $reflectionClass->getProperty('userInfo');
        $reflectionProperty->setValue(
            $request,
            new VerificationUserInfoDTO($userInfo ?: self::REQUEST_USER_INFO)
        );
    }
}
