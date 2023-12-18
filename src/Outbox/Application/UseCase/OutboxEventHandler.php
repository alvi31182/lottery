<?php

declare(strict_types=1);

namespace App\Outbox\Application\UseCase;

use App\Lottery\Infrastructure\Outbox\OutboxInterface;
use App\Lottery\Model\Events\AwardCreated;
use App\Outbox\Model\Outbox;
use App\Outbox\Model\WriteOutboxStorage;
use Exception;
use DateTimeImmutable;

final readonly class OutboxEventHandler implements OutboxInterface
{
    public function __construct(
        private WriteOutboxStorage $outboxStorage,
    ) {
    }

    /**
     * @throws Exception
     */
    public function addToOutbox(AwardCreated $domainEvent): void
    {
        $outbox = Outbox::create($domainEvent, $this->getEventData($domainEvent));
        $this->outboxStorage->createOutbox(outbox: $outbox);
    }

    /**
     * @return array{
     *     message_event: string,
     *     lottery: array{
     *         lottery_id: string,
     *         win_sum: string,
     *         created_at: DateTimeImmutable
     *     }
     * }
     *
     * @throws Exception
     */
    private function getEventData(AwardCreated $awardCreated): array
    {
        return [
            'message_event' => 'lottery.v1.awarded',
            'lottery' => [
                'lottery_id' => $awardCreated->lotteryId,
                'win_sum' => $awardCreated->winSum,
                'created_at' => $awardCreated->occurredOn(),
            ],
        ];
    }
}
