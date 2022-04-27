<?php

declare(strict_types=1);

namespace App\Service;

use App\Component\Request\Template\VerificationCodeTemplateRequest;
use App\Component\Response\Template\VerificationCodeTemplateResponse;
use App\Enums\TemplateSlug;
use App\Exception\TemplateNotFoundException;

class TemplateService
{
    private const TEMPLATE_SLUG_VIEW_MAP = [
        TemplateSlug::EMAIL_VERIFICATION => '@verification/email-verification.html.twig',
        TemplateSlug::MOBILE_VERIFICATION => '@verification/mobile-verification.txt.twig',
    ];

    /**
     * @throws TemplateNotFoundException
     */
    public function resolveVerificationCodeTemplate(
        VerificationCodeTemplateRequest $request
    ): VerificationCodeTemplateResponse {
        if (!array_key_exists($request->getSlug(), self::TEMPLATE_SLUG_VIEW_MAP)) {
            throw new TemplateNotFoundException();
        }

        return new VerificationCodeTemplateResponse(
            self::TEMPLATE_SLUG_VIEW_MAP[$request->getSlug()],
            $request->getVariables()->getCode()
        );
    }
}
