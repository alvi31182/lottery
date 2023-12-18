<?php

declare(strict_types=1);

namespace App\Lottery\Application\Events\DomainEvents\Subscriber;

use App\Lottery\Model\Events\DomainEvent;

interface DomainEventSubscriber
{
    public function handleEvent(DomainEvent $domainEvent): void;
}
