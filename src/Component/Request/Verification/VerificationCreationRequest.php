<?php

declare(strict_types=1);

namespace App\Component\Request\Template;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Component\DTO\Request\VerificationUserInfoDTO;
use App\Component\Request\AbstractJsonBodyRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationCreationRequest extends AbstractJsonBodyRequest
{
    protected const EXCEPTION = 'Validation failed: invalid subject supplied.';

    /** @var VerificationSubjectDTO */
    #[Assert\Valid]
    protected $subject;

    /** @var VerificationUserInfoDTO */
    #[Assert\Valid]
    protected $userInfo;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->subject = new VerificationSubjectDTO(
            is_array($this->subject) ? $this->subject : []
        );
        $this->userInfo = new VerificationUserInfoDTO([
            'clientIp' => $request->getClientIp(),
            'userAgent' => $request->headers->get('User-Agent'),
        ]);
    }

    public function getSubject(): VerificationSubjectDTO
    {
        return $this->subject;
    }

    public function getUserInfo(): VerificationUserInfoDTO
    {
        return $this->userInfo;
    }
}
