<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Enums\TemplateSlug;
use App\Helper\VerificationCodeGenerationHelper;
use App\Tests\AbstractHttpClientWebTestCase;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\TemplateController
 *
 * @internal
 */
class TemplateControllerTest extends AbstractHttpClientWebTestCase
{
    private const RESOURCES_PATH = __DIR__.'/../Resources/';

    /**
     * @dataProvider templateSlugDataProvider
     *
     * @throws JsonException
     */
    public function testVerificationCodeTemplateRendered(string $slug): void
    {
        $verificationCode = VerificationCodeGenerationHelper::generateVerificationCode(8);
        $this->sendVerificationCodeTemplateRequest($slug, ['code' => $verificationCode]);

        $expectedContent = $this->getExpectedTemplateWithCode(
            $slug,
            $verificationCode
        );

        self::assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertResponseHasContent($expectedContent);
    }

    #[ArrayShape(['email' => 'array', 'mobile' => 'array'])]
    public function templateSlugDataProvider(): array
    {
        return [
            'email' => [TemplateSlug::EMAIL_VERIFICATION],
            'mobile' => [TemplateSlug::MOBILE_VERIFICATION],
        ];
    }

    /**
     * @dataProvider invalidTemplateParametersDataProvider
     */
    public function testVerificationCodeTemplateRequestValidationFailed(mixed $slug, mixed $variables): void
    {
        $this->sendVerificationCodeTemplateRequest($slug, $variables);

        $expectedResponse = [
            'message' => 'Validation failed: invalid / missing variables supplied.',
        ];

        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertResponseHasJson($expectedResponse);
    }

    #[ArrayShape([
        'nullable slug' => 'array',
        'non-string slug' => 'array',
        'variables with nullable code' => 'array',
        'variables with non-string code' => 'array',
        'variables with non-digit code' => 'array',
    ])]
    public function invalidTemplateParametersDataProvider(): array
    {
        $correctSlug = TemplateSlug::MOBILE_VERIFICATION;
        $correctCode = VerificationCodeGenerationHelper::generateVerificationCode(8);

        return [
            'nullable slug' => [null, ['code' => $correctCode]],
            'non-string slug' => [true, ['code' => $correctCode]],
            'variables with nullable code' => [$correctSlug, []],
            'variables with non-string code' => [
                $correctSlug,
                ['code' => true],
            ],
            'variables with non-digit code' => [
                $correctSlug,
                ['code' => 'xxx-aaa-1'],
            ],
        ];
    }

    public function testTVerificationCodeTemplateMalformedJsonExceptionThrown(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/templates/render',
            [],
            [],
            [],
            '{...}'
        );

        $expectedResponse = [
            'message' => 'Malformed JSON passed.',
        ];

        self::assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertResponseHasJson($expectedResponse);
    }

    public function testVerificationCodeTemplateNotFoundExceptionThrown(): void
    {
        // TODO: implement BE service & adjust logic
    }

    private function getExpectedTemplateWithCode(string $slug, string $code): string
    {
        $template = match ($slug) {
            TemplateSlug::EMAIL_VERIFICATION => self::RESOURCES_PATH.'email-verification.html',
            TemplateSlug::MOBILE_VERIFICATION => self::RESOURCES_PATH.'mobile-verification.txt'
        };

        $content = file_get_contents($template);

        return str_replace('{{ code }}', $code, $content);
    }

    private function sendVerificationCodeTemplateRequest(mixed $slug, mixed $variables): void
    {
        $this->sendPostRequest(
            '/templates/render',
            [
                'slug' => $slug,
                'variables' => $variables,
            ]
        );
    }
}
