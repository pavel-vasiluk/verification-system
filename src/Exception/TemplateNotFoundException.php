<?php

declare(strict_types=1);

namespace App\Exception;

use JetBrains\PhpStorm\Pure;
use Symfony\Component\HttpFoundation\Response;

class TemplateNotFoundException extends AbstractRequestException
{
    protected $message = 'Template not found.';

    #[Pure]
    public function __construct()
    {
        parent::__construct($this->message, Response::HTTP_NOT_FOUND);
    }
}
