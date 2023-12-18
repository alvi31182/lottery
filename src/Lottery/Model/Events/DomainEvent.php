<?php

declare(strict_types=1);

namespace App\Lottery\Model\Events;

use DateTimeImmutable;

interface DomainEvent
{
    public function occurredOn(): DateTimeImmutable;

    public function eventName(): string;
}
