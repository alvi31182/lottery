<?php

declare(strict_types=1);

namespace App\Outbox\Application\Exception;

use Symfony\Component\Config\Definition\Exception\Exception;
use Throwable;

final class OutboxUpdateException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
