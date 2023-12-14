<?php

declare(strict_types=1);

namespace App\Core\Exception;

final class Error
{
    public function __construct(
        private string $title
    ) {
    }
}
