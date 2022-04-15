<?php

declare(strict_types=1);

namespace App\Exception;

use JsonSerializable;

interface RequestExceptionInterface extends JsonSerializable
{
    public function jsonSerialize(): array;
}
