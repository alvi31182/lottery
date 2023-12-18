<?php

declare(strict_types=1);

namespace App\Lottery\Application\Events\DomainEvents\Subscriber;

use App\Lottery\Model\Events\AwardCreated;

interface DomainEventSubscriber
{
    public function handleEvent(AwardCreated $domainEvent): void;
}
