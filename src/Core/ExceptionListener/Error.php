<?php

declare(strict_types=1);

namespace App\Core\ExceptionListener;

final class Error
{
    public function __construct(
        private string $title
    ) {
    }
}
