<?php

declare(strict_types=1);

namespace App\Component\Request\Verification;

use App\Component\DTO\Request\VerificationSubjectDTO;
use App\Component\Request\AbstractUserInfoAwareRequest;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class VerificationCreationRequest extends AbstractUserInfoAwareRequest
{
    protected const EXCEPTION = 'Validation failed: invalid subject supplied.';

    /** @var VerificationSubjectDTO */
    #[Assert\Valid]
    protected $subject;

    public function __construct(Request $request)
    {
        parent::__construct($request);

        $this->subject = new VerificationSubjectDTO(
            is_array($this->subject) ? $this->subject : []
        );
    }

    public function getSubject(): VerificationSubjectDTO
    {
        return $this->subject;
    }
}
