<?php

declare(strict_types=1);

namespace App\Outbox\Model;

interface WriteOutboxStorage
{
    public function createOutbox(Outbox $outbox): void;
}
