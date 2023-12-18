<?php

declare(strict_types=1);

namespace App\Lottery\Model\Events;

use App\Lottery\Application\Events\DomainEvents\Subscriber\DomainEventSubscriber;
use SplQueue;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
trait AggregateRoot
{
    private SplQueue $domainEvents;

    private DomainEvent $domainEvent;

    private array $eventSubscribers = [];

    public function init(): void
    {
        $this->domainEvents = new SplQueue();
    }

    public function addEventSubscriber(DomainEventSubscriber $subscriber): void
    {
        $this->eventSubscribers[] = $subscriber;
    }

    public function pullDomainEvents(): SplQueue
    {
        $domainEvents = $this->domainEvents;
        $this->domainEvents = new SplQueue();
        return $domainEvents;
    }

    protected function recordEvent(DomainEvent $domainEvent): void
    {
            $this->domainEvents->enqueue($domainEvent);
            $this->publishEvent(domainEvent: $domainEvent);
    }

    protected function publishEvent(DomainEvent $domainEvent): void
    {
        /** @var DomainEventSubscriber $subscriber */
        foreach ($this->eventSubscribers as $subscriber) {
            $subscriber->handleEvent(domainEvent: $domainEvent);
        }
    }
}
