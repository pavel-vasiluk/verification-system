<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Request\Template\TemplateRenderRequest;
use App\Enums\TemplateSlug;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/templates/render', name: 'templates_render', methods: [Request::METHOD_POST])]
    public function renderTemplate(TemplateRenderRequest $request): Response
    {
        $code = $request->getVariables()->getCode();

        if (TemplateSlug::MOBILE_VERIFICATION === $request->getSlug()) {
            return $this->render('@verification/mobile-verification.txt.twig', ['code' => $code]);
        }

        return $this->render('@verification/email-verification.html.twig', ['code' => $code]);
    }
}
