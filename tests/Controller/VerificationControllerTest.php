<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Entity\Notification;
use App\Entity\Verification;
use App\Enums\ConfirmationTypes;
use App\Helper\VerificationCodeGenerationHelper;
use App\Repository\VerificationRepository;
use App\Tests\AbstractHttpClientWebTestCase;
use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers \App\Controller\VerificationController
 *
 * @internal
 */
class VerificationControllerTest extends AbstractHttpClientWebTestCase
{
    private VerificationRepository $verificationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verificationRepository = $this->container->get(VerificationRepository::class);

        $this->truncateEntities([
            Verification::class,
            Notification::class,
        ]);
    }

    /**
     * @dataProvider verificationDataProvider
     */
    public function testVerificationCreated(string $identity, string $type): void
    {
        $verificationSubject = new VerificationSubjectDTO([
            'identity' => $identity,
            'type' => $type,
        ]);

        $this->createVerificationFromSubject($verificationSubject);
    }

    /**
     * @dataProvider invalidVerificationCreationRequestParametersDataProvider
     */
    public function testVerificationCreationRequestValidationFailed(mixed $identity, mixed $type): void
    {
        $this->sendVerificationCreationRequest(['identity' => $identity, 'type' => $type]);
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $expectedResponse = [
            'message' => 'Validation failed: invalid subject supplied.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    #[ArrayShape([
        'nullable identity' => 'array',
        'non-string identity' => 'array',
        'invalid email identity' => 'array',
        'invalid mobile identity' => 'array',
        'nullable type' => 'array',
        'non-string type' => 'array',
        'type of non-existing choice' => 'string[]',
    ])]
    public function invalidVerificationCreationRequestParametersDataProvider(): array
    {
        $correctEmailIdentity = 'john.doe@abc.xyz';
        $correctEmailConfirmationType = ConfirmationTypes::EMAIL_CONFIRMATION;
        $correctMobileIdentity = '+37120000001';
        $correctMobileConfirmationType = ConfirmationTypes::MOBILE_CONFORMATION;

        return [
            'nullable identity' => [null, $correctEmailConfirmationType],
            'non-string identity' => [true, $correctEmailConfirmationType],
            'invalid email identity' => [$correctMobileIdentity, $correctEmailConfirmationType],
            'invalid mobile identity' => [$correctEmailIdentity, $correctMobileConfirmationType],
            'nullable type' => [$correctEmailIdentity, null],
            'non-string type' => [$correctEmailIdentity, true],
            'type of non-existing choice' => [$correctEmailIdentity, '2fa_confirmation'],
        ];
    }

    public function testVerificationCreationMalformedJsonExceptionThrown(): void
    {
        $this->client->request(
            Request::METHOD_POST,
            '/verifications',
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

    public function testDuplicatedVerificationCreationFailed(): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());
        $this->sendVerificationCreationRequest($existingVerification->getSubject());

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $expectedResponse = [
            'message' => 'Duplicated verification.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    /**
     * @dataProvider verificationDataProvider
     */
    public function testVerificationConfirmed(string $identity, string $type): void
    {
        $verificationSubject = new VerificationSubjectDTO([
            'identity' => $identity,
            'type' => $type,
        ]);
        $existingVerification = $this->createVerificationFromSubject($verificationSubject);

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            $existingVerification->getCode()
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    #[ArrayShape(['email notification' => 'array', 'sms notification' => 'array'])]
    public function verificationDataProvider(): array
    {
        return [
            'email notification' => [
                'john.doe@abc.xyz',
                ConfirmationTypes::EMAIL_CONFIRMATION,
            ],
            'sms notification' => [
                '+37120000001',
                ConfirmationTypes::MOBILE_CONFORMATION,
            ],
        ];
    }

    /**
     * @dataProvider invalidVerificationConfirmationRequestParametersDataProvider
     */
    public function testVerificationConfirmationRequestValidationFailed(mixed $code): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            $code
        );
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $expectedResponse = [
            'message' => 'Validation failed: invalid code supplied.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    #[ArrayShape([
        'nullable code' => 'null[]',
        'non-string code' => 'int[]',
        'non-digit code' => 'string[]',
    ])]
    public function invalidVerificationConfirmationRequestParametersDataProvider(): array
    {
        return [
            'nullable code' => [null],
            'non-string code' => [12345678],
            'non-digit code' => ['a7zjsjsd'],
        ];
    }

    public function testNonExistingVerificationConfirmationFailed(): void
    {
        $this->sendVerificationConfirmationRequest(
            Uuid::uuid4()->toString(),
            VerificationCodeGenerationHelper::generateVerificationCode(8)
        );
        self::assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);

        $expectedResponse = [
            'message' => 'Verification not found.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    public function testConfirmVerificationHavingDifferentClientFailed(): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());
        $existingVerification->setUserInfo(
            array_merge($existingVerification->getUserInfo(), ['clientIp' => '0.0.0.0'])
        );

        $this->entityManager->flush();

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            $existingVerification->getCode()
        );
        self::assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);

        $expectedResponse = [
            'message' => 'No permission to confirm verification.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    public function testConfirmExpiredVerificationFailed(): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());
        $existingVerification->setIsExpired(true);

        $this->entityManager->flush();

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            $existingVerification->getCode()
        );
        self::assertResponseStatusCodeSame(Response::HTTP_GONE);

        $expectedResponse = [
            'message' => 'Verification expired.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    public function testConfirmExpiredByTimeVerificationFailed(): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());

        Carbon::setTestNow(Carbon::now()->addHour());

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            $existingVerification->getCode()
        );
        self::assertResponseStatusCodeSame(Response::HTTP_GONE);

        $expectedResponse = [
            'message' => 'Verification expired.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    public function testConfirmVerificationWithInvalidCodeFailed(): void
    {
        $existingVerification = $this->createVerificationFromSubject($this->prepareRandomVerificationSubject());

        $this->sendVerificationConfirmationRequest(
            $existingVerification->getId()->toString(),
            VerificationCodeGenerationHelper::generateVerificationCode(8),
        );
        self::assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);

        $expectedResponse = [
            'message' => 'Validation failed: invalid code supplied.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    private function sendVerificationCreationRequest(array $subject): void
    {
        $this->sendPostRequest('/verifications', ['subject' => $subject]);
    }

    private function sendVerificationConfirmationRequest(string $verificationUuid, mixed $code): void
    {
        $this->sendPutRequest(
            sprintf('/verifications/%s/confirm', $verificationUuid),
            ['code' => $code]
        );
    }

    private function createVerificationFromSubject(VerificationSubjectDTO $verificationSubject): Verification
    {
        $this->sendVerificationCreationRequest($verificationSubject->jsonSerialize());
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $verificationIdMatches = $this->verificationRepository->findIdsBySubject($verificationSubject);
        $this->assertCount(1, $verificationIdMatches);
        $verificationUuid = $verificationIdMatches[0]['id'];

        $expectedResponse = ['id' => $verificationUuid];

        $this->assertResponseHasJson($expectedResponse);
        $verification = $this->verificationRepository->find($verificationUuid);
        $this->assertNotNull($verification);

        return $verification;
    }

    private function prepareRandomVerificationSubject(): VerificationSubjectDTO
    {
        return new VerificationSubjectDTO([
            'identity' => 'john.doe@abc.xyz',
            'type' => ConfirmationTypes::EMAIL_CONFIRMATION,
        ]);
    }
}
