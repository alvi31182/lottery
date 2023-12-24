<?php

declare(strict_types=1);

namespace App\Outbox\Application\Exception;

use Exception;
use Throwable;

final class OutboxGetListException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
