<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Component\DTO\Request\VerificationUserInfoDTO;
use App\Component\Request\Verification\VerificationCreationRequest;
use App\Entity\Verification;
use App\Enums\ConfirmationTypes;
use App\Exception\DuplicatedVerificationException;
use App\Helper\VerificationCodeGenerationHelper;
use App\Message\Verification\VerificationCreatedMessage;
use App\Repository\VerificationRepository;
use App\Service\VerificationService;
use App\Tests\AbstractWebTestCase;
use JetBrains\PhpStorm\ArrayShape;
use JsonException;
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
     * @dataProvider verificationCreationDataProvider
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

    #[ArrayShape(['email notification' => 'array', 'sms notification' => 'array'])]
    public function verificationCreationDataProvider(): array
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
        $request = new Request();
        $request->initialize([], [], [], [], [], [], json_encode($requestBody, JSON_THROW_ON_ERROR));

        $verificationCreationRequest = new VerificationCreationRequest($request);

        $reflectionClass = new ReflectionClass($verificationCreationRequest);
        $reflectionProperty = $reflectionClass->getProperty('userInfo');
        $reflectionProperty->setValue(
            $verificationCreationRequest,
            new VerificationUserInfoDTO(self::REQUEST_USER_INFO)
        );

        return $verificationCreationRequest;
    }
}
