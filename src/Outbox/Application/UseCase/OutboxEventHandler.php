<?php

declare(strict_types=1);

namespace App\Outbox\Application\UseCase;

use App\Lottery\Infrastructure\Outbox\OutboxInterface;
use App\Lottery\Model\Events\DomainEvent;

final class OutboxEventHandler implements OutboxInterface
{
    public function addToOutbox(DomainEvent $domainEvent): void
    {
        /**__**/
    }
}
