<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Request\Template\VerificationCreationRequest;
use App\Service\VerificationService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VerificationController extends AbstractController
{
    #[Route('/verifications', name: 'verification_create', methods: [Request::METHOD_POST])]
    public function createVerification(
        VerificationCreationRequest $request,
        VerificationService $verificationService
    ): JsonResponse {
        return new JsonResponse(
            $verificationService->createVerification($request),
            Response::HTTP_CREATED
        );
    }

    #[Route(
        '/verifications/{id}/confirm',
        name: 'verification_confirm',
        requirements: ['id' => '[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}'],
        methods: [Request::METHOD_PUT],
    )]
    public function confirmVerification(
    ): JsonResponse {
        return new JsonResponse([], Response::HTTP_NO_CONTENT);
    }
}
