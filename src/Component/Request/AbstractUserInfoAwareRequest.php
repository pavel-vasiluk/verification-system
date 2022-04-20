<?php

declare(strict_types=1);

namespace App\Component\Request;

use App\Component\DTO\Request\VerificationUserInfoDTO;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

abstract class AbstractUserInfoAwareRequest extends AbstractJsonBodyRequest
{
    /** @var VerificationUserInfoDTO */
    #[Assert\Valid]
    protected $userInfo;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->userInfo = new VerificationUserInfoDTO([
            'clientIp' => $request->getClientIp(),
            'userAgent' => $request->headers->get('User-Agent'),
        ]);
    }

    public function getUserInfo(): VerificationUserInfoDTO
    {
        return $this->userInfo;
    }
}
