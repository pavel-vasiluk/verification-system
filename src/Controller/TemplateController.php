<?php

declare(strict_types=1);

namespace App\Controller;

use App\Component\Request\Template\TemplateRenderRequest;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TemplateController extends AbstractController
{
    #[Route('/templates/render', name: 'templates_render', methods: [Request::METHOD_POST])]
    public function renderTemplate(TemplateRenderRequest $request): Response
    {
        return new Response('Hello');
    }
}
