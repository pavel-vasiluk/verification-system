<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Component\DTO\Request\VerificationUserInfoDTO;
use App\Component\Request\AbstractUserInfoAwareRequest;
use App\Component\Request\Verification\VerificationConfirmationRequest;
use App\Component\Request\Verification\VerificationCreationRequest;
use App\Entity\Verification;
use App\Enums\ConfirmationTypes;
use App\Exception\DuplicatedVerificationException;
use App\Exception\InvalidVerificationCodeException;
use App\Exception\VerificationAccessDeniedException;
use App\Exception\VerificationExpiredException;
use App\Exception\VerificationNotFoundException;
use App\Helper\VerificationCodeGenerationHelper;
use App\Message\Verification\VerificationCreatedMessage;
use App\Repository\VerificationRepository;
use App\Service\VerificationService;
use App\Tests\AbstractWebTestCase;
use Carbon\Carbon;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
use Ramsey\Uuid\Uuid;
use ReflectionClass;
use Symfony\Component\HttpFoundation\Request;

/**
 * @covers \App\Service\VerificationService
 *
 * @internal
 */
class VerificationServiceTest extends AbstractWebTestCase
{
    private VerificationService $verificationService;
    private VerificationRepository $verificationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->verificationService = $this->container->get(VerificationService::class);
        $this->verificationRepository = $this->container->get(VerificationRepository::class);

        $this->truncateEntities([
            Verification::class,
        ]);
    }

    /**
     * @dataProvider verificationDataProvider
     */
    public function testVerificationCreated(string $identity, string $type): void
    {
        $this->assertEmpty($this->messageTransport->get());

        $verificationCreationRequest = $this->prepareVerificationCreationRequest([
            'subject' => [
                'identity' => $identity,
                'type' => $type,
            ],
        ]);

        $verificationCreationResponse = $this->verificationService->createVerification($verificationCreationRequest);
        $verification = $this->verificationRepository->find($verificationCreationResponse->getId());

        $this->assertNotNull($verification);
        $this->assertEqualsCanonicalizing($verificationCreationRequest->getSubject()->jsonSerialize(), $verification->getSubject());
        $this->assertEqualsCanonicalizing($verificationCreationRequest->getUserInfo()->jsonSerialize(), $verification->getUserInfo());

        $envelopes = $this->messageTransport->get();
        $this->assertCount(1, $envelopes);

        $envelopeMessage = $envelopes[0]->getMessage();
        $this->assertInstanceOf(VerificationCreatedMessage::class, $envelopeMessage);
        $this->assertSame($verification->getId()->toString(), $envelopeMessage->getId());
    }

    public function testExceptionThrownWhenTryingToCreateDuplicateVerification(): void
    {
        $existingVerification = $this->prepareVerification();
        $verificationCreationRequest = $this->prepareVerificationCreationRequest([
            'subject' => [
                'identity' => $existingVerification->getSubject()['identity'],
                'type' => $existingVerification->getSubject()['type'],
            ],
        ]);

        $this->expectException(DuplicatedVerificationException::class);
        $this->verificationService->createVerification($verificationCreationRequest);
        $this->assertEmpty($this->messageTransport->get());
    }

    /**
     * @dataProvider verificationDataProvider
     */
    public function testVerificationConfirmation(string $identity, string $type): void
    {
        $existingVerification = $this->prepareVerification([
            'identity' => $identity,
            'type' => $type,
        ]);

        $this->assertFalse($existingVerification->isConfirmed());
        $this->assertFalse($existingVerification->isExpired());

        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => $existingVerification->getCode()],
            ['id' => $existingVerification->getId()->toString()],
        );

        $this->verificationService->confirmVerification($verificationCreationRequest);

        $this->assertTrue($existingVerification->isConfirmed());
        $this->assertTrue($existingVerification->isExpired());
    }

    public function testExceptionThrownWhenTryingToConfirmNonExistingVerification(): void
    {
        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => VerificationCodeGenerationHelper::generateVerificationCode(8)],
            ['id' => Uuid::uuid4()->toString()],
        );

        $this->expectException(VerificationNotFoundException::class);

        $this->verificationService->confirmVerification($verificationCreationRequest);
    }

    public function testExceptionThrownWhenTryingToConfirmVerificationWithDifferentClient(): void
    {
        $existingVerification = $this->prepareVerification();

        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => $existingVerification->getCode()],
            ['id' => $existingVerification->getId()->toString()],
            array_merge(self::REQUEST_USER_INFO, ['clientIp' => '0.0.0.1'])
        );

        $this->expectException(VerificationAccessDeniedException::class);

        $this->verificationService->confirmVerification($verificationCreationRequest);
    }

    public function testExceptionThrownWhenTryingToConfirmExpiredVerification(): void
    {
        $existingVerification = $this->prepareVerification();
        $existingVerification->setIsExpired(true);

        $this->entityManager->flush();

        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => $existingVerification->getCode()],
            ['id' => $existingVerification->getId()->toString()],
        );

        $this->expectException(VerificationExpiredException::class);

        $this->verificationService->confirmVerification($verificationCreationRequest);
    }

    public function testExceptionThrownWhenTryingToConfirmExpiredByTimeVerification(): void
    {
        $existingVerification = $this->prepareVerification();

        Carbon::setTestNow(Carbon::now()->addHour());

        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => $existingVerification->getCode()],
            ['id' => $existingVerification->getId()->toString()],
        );

        $this->expectException(VerificationExpiredException::class);

        $this->verificationService->confirmVerification($verificationCreationRequest);
    }

    public function testExceptionThrownWhenTryingToConfirmVerificationWithInvalidVerificationCode(): void
    {
        $existingVerification = $this->prepareVerification();

        $this->assertSame(0, $existingVerification->getConfirmationAttempts());

        $verificationCreationRequest = $this->prepareVerificationConfirmationRequest(
            ['code' => VerificationCodeGenerationHelper::generateVerificationCode(8)],
            ['id' => $existingVerification->getId()->toString()],
        );

        $this->expectException(InvalidVerificationCodeException::class);

        try {
            $this->verificationService->confirmVerification($verificationCreationRequest);
        } finally {
            $this->assertSame(1, $existingVerification->getConfirmationAttempts());
        }
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

    private function prepareVerification(array $subject = [], array $userInfo = []): Verification
    {
        $verification = (new Verification())
            ->setCode(VerificationCodeGenerationHelper::generateVerificationCode(8))
            ->setSubject($subject ?: $this->prepareVerificationSubject()->jsonSerialize())
            ->setUserInfo($userInfo ?: self::REQUEST_USER_INFO)
        ;

        $this->entityManager->persist($verification);
        $this->entityManager->flush();

        return $verification;
    }

    private function prepareVerificationSubject(): VerificationSubjectDTO
    {
        return new VerificationSubjectDTO([
            'identity' => 'john.doe@abc.xyz',
            'type' => ConfirmationTypes::EMAIL_CONFIRMATION,
        ]);
    }

    /**
     * @throws JsonException
     */
    private function prepareVerificationCreationRequest(array $requestBody): VerificationCreationRequest
    {
        $request = $this->prepareHttpRequestWithBody($requestBody);
        $verificationCreationRequest = new VerificationCreationRequest($request);

        $this->setRequestUserInfo($verificationCreationRequest);

        return $verificationCreationRequest;
    }

    /**
     * @throws JsonException
     */
    private function prepareVerificationConfirmationRequest(
        array $requestBody,
        array $attributes = [],
        array $userInfo = []
    ): VerificationConfirmationRequest {
        $request = $this->prepareHttpRequestWithBody($requestBody, $attributes);
        $verificationConfirmationRequest = new VerificationConfirmationRequest($request);

        $this->setRequestUserInfo($verificationConfirmationRequest, $userInfo);

        return $verificationConfirmationRequest;
    }

    /**
     * @throws JsonException
     */
    private function prepareHttpRequestWithBody(array $requestBody, array $attributes = []): Request
    {
        $request = new Request();
        $request->initialize([], [], $attributes, [], [], [], json_encode($requestBody, JSON_THROW_ON_ERROR));

        return $request;
    }

    private function setRequestUserInfo(AbstractUserInfoAwareRequest $request, array $userInfo = []): void
    {
        $reflectionClass = new ReflectionClass($request);
        $reflectionProperty = $reflectionClass->getProperty('userInfo');
        $reflectionProperty->setValue(
            $request,
            new VerificationUserInfoDTO($userInfo ?: self::REQUEST_USER_INFO)
        );
    }
}
