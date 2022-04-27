<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\Request\Template\VerificationCodeTemplateRequest;
use App\Enums\TemplateSlug;
use App\Exception\TemplateNotFoundException;
use App\Helper\VerificationCodeGenerationHelper;
use App\Service\TemplateService;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;

/**
 * @covers \App\Service\NotificationService
 *
 * @internal
 */
class TemplateServiceTest extends AbstractServiceTestCase
{
    private TemplateService $templateService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->templateService = $this->container->get(TemplateService::class);
    }

    /**
     * @dataProvider verificationCodeTemplateDataProvider
     */
    public function testExpectedVerificationCodeTemplateReturned(string $slug, string $expectedTemplate): void
    {
        $verificationCode = VerificationCodeGenerationHelper::generateVerificationCode(8);

        $verificationCodeTemplateRequest = $this->prepareVerificationCodeTemplateRequest([
            'slug' => $slug,
            'variables' => [
                'code' => $verificationCode,
            ],
        ]);
        $verificationCodeTemplateResponse = $this->templateService->resolveVerificationCodeTemplate(
            $verificationCodeTemplateRequest
        );

        $this->assertSame($verificationCode, $verificationCodeTemplateResponse->getCode());
        $this->assertSame($expectedTemplate, $verificationCodeTemplateResponse->getTemplate());
    }

    #[ArrayShape(['email template' => 'array', 'mobile template' => 'array'])]
    public function verificationCodeTemplateDataProvider(): array
    {
        return [
            'email template' => [
                TemplateSlug::EMAIL_VERIFICATION,
                '@verification/email-verification.html.twig',
            ],
            'mobile template' => [
                TemplateSlug::MOBILE_VERIFICATION,
                '@verification/mobile-verification.txt.twig',
            ],
        ];
    }

    public function testTemplateNotFoundExceptionThrownWhenInvalidSlugProvided(): void
    {
        $verificationCode = VerificationCodeGenerationHelper::generateVerificationCode(8);

        $verificationCodeTemplateRequest = $this->prepareVerificationCodeTemplateRequest([
            'slug' => '2fa-verification',
            'variables' => [
                'code' => $verificationCode,
            ],
        ]);

        $this->expectException(TemplateNotFoundException::class);

        $this->templateService->resolveVerificationCodeTemplate(
            $verificationCodeTemplateRequest
        );
    }

    /**
     * @throws JsonException
     */
    private function prepareVerificationCodeTemplateRequest(array $requestBody): VerificationCodeTemplateRequest
    {
        $request = $this->prepareHttpRequestWithBody($requestBody);

        return new VerificationCodeTemplateRequest($request);
    }
}
