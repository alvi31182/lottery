<?php

declare(strict_types=1);

namespace App\Lottery\Shared\Outbox;

use App\Lottery\Model\Events\AwardCreated;

interface OutboxInterface
{
    public function addToOutbox(AwardCreated $domainEvent): void;
}
