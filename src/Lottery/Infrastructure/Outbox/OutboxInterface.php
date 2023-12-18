<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Outbox;

use App\Lottery\Model\Events\DomainEvent;

interface OutboxInterface
{
    public function addToOutbox(DomainEvent $domainEvent): void;
}
