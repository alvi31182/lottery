<?php

declare(strict_types=1);

namespace App\Outbox\Model;

use Traversable;

interface ReadOutboxStorage
{
    /**
     * @return Traversable<int,array<string,mixed>>
     */
    public function getNotSendOutboxData(): iterable;
}
