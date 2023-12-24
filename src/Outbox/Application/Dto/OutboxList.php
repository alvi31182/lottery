<?php

declare(strict_types=1);

namespace App\Outbox\Application\Dto;

final readonly class OutboxList
{
    public function __construct(
        public string $outboxId,
        public bool $isSend,
        public string $eventData
    ) {
    }
}
