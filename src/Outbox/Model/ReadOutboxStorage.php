<?php

declare(strict_types=1);

namespace App\Outbox\Model;

use App\Outbox\Application\Dto\OutboxList;

interface ReadOutboxStorage
{
    /**
     * @return iterable<OutboxList>
     */
    public function getNotSendOutboxData(): iterable;

    public function findById(string $id): ?Outbox;
}
