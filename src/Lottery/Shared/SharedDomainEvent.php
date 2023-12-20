<?php

declare(strict_types=1);

namespace App\Lottery\Shared;

use App\Lottery\Application\Events\DomainEvents\Subscriber\DomainEventSubscriber;
use App\Lottery\Infrastructure\Outbox\OutboxInterface;
use App\Lottery\Model\Events\DomainEvent;

final readonly class SharedDomainEvent implements DomainEventSubscriber
{
    public function __construct(
        private OutboxInterface $outbox
    ) {
    }
    public function handleEvent(DomainEvent $domainEvent): void
    {
        $this->outbox->addToOutbox(domainEvent: $domainEvent);
    }
}
