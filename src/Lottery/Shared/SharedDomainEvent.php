<?php

declare(strict_types=1);

namespace App\Lottery\Shared;

use App\Lottery\Application\Events\DomainEvents\Subscriber\DomainEventSubscriber;
use App\Lottery\Model\Events\DomainEvent;
use App\Lottery\Shared\Outbox\OutboxInterface;

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
