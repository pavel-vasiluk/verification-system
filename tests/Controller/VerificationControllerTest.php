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
use JetBrains\PhpStorm\ArrayShape;
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
     * @dataProvider verificationCreationDataProvider
     */
    public function testVerificationCreation(string $identity, string $type): void
    {
        $verificationSubject = new VerificationSubjectDTO([
            'identity' => $identity,
            'type' => $type,
        ]);

        $this->sendVerificationCreationRequest($verificationSubject->jsonSerialize());
        self::assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $verificationIdMatches = $this->verificationRepository->findIdsBySubject($verificationSubject);
        $this->assertCount(1, $verificationIdMatches);

        $expectedResponse = [
            'id' => $verificationIdMatches[0]['id'],
        ];

        $this->assertResponseHasJson($expectedResponse);
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
        $existingVerification = $this->prepareVerification();
        $this->sendVerificationCreationRequest($existingVerification->getSubject());

        self::assertResponseStatusCodeSame(Response::HTTP_CONFLICT);

        $expectedResponse = [
            'message' => 'Duplicated verification.',
        ];

        $this->assertResponseHasJson($expectedResponse);
    }

    private function sendVerificationCreationRequest(array $subject): void
    {
        $this->sendPostRequest('/verifications', ['subject' => $subject]);
    }

    private function sendVerificationConfirmationRequest(string $verificationUuid, array $subject): void
    {
        $this->sendPutRequest(
            sprintf('/verifications/%s/confirm', $verificationUuid),
            ['subject' => $subject]
        );
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
}
