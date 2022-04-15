<?php

declare(strict_types=1);

namespace App\Exception;

use Exception;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

class AbstractRequestException extends Exception implements RequestExceptionInterface
{
    #[Pure]
    #[ArrayShape(['message' => 'string'])]
    public function jsonSerialize(): array
    {
        return [
            'message' => $this->getMessage(),
        ];
    }
}
