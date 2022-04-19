<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class DuplicatedVerificationException extends AbstractRequestException
{
    protected $message = 'Duplicated verification.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_CONFLICT);
    }
}
