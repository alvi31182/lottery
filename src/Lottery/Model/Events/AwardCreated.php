<?php

declare(strict_types=1);

namespace App\Lottery\Model\Events;

use DateTimeImmutable;
use DateTimeZone;
use Exception;

final class AwardCreated implements DomainEvent
{
    /**
     * @throws Exception
     */
    public function __construct(
        public readonly string $aggregateId,
        public readonly string $lotteryId,
        public readonly string $awardStatus,
        public readonly string $winSum,
        public ?DateTimeImmutable $occurredOn = null,
    ) {
        $this->occurredOn = $this->occurredOn ?: $this->occurredOn();
    }
    /**
     * @throws Exception
     */
    public function occurredOn(): DateTimeImmutable
    {
        return new DateTimeImmutable(datetime: 'now', timezone: new DateTimeZone('UTC'));
    }

    public function eventName(): string
    {
        return self::class;
    }
}
