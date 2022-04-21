<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class VerificationAccessDeniedException extends AbstractRequestException
{
    protected $message = 'No permission to confirm verification.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_FORBIDDEN);
    }
}
