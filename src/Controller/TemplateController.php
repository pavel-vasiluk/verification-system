<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Request\Template\VerificationCodeTemplateRequest;
use App\Service\TemplateService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/templates/render', name: 'templates_render', methods: [Request::METHOD_POST])]
    public function renderTemplate(
        VerificationCodeTemplateRequest $request,
        TemplateService $templateService
    ): Response {
        $templateResponse = $templateService->resolveVerificationCodeTemplate($request);

        return $this->render($templateResponse->getTemplate(), ['code' => $templateResponse->getCode()]);
    }
}
