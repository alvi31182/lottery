<?php

declare(strict_types=1);

namespace Outbox\Application\UseCase;

use App\Lottery\Model\Events\AwardCreated;
use App\Outbox\Application\UseCase\OutboxEventHandler;
use App\Outbox\Model\Outbox;
use App\Outbox\Model\WriteOutboxStorage;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class OutboxEventHandlerTest extends TestCase
{
    public function testAddToOutbox(): void
    {
        $outboxStorageMock = $this->createMock(WriteOutboxStorage::class);
        $logger = $this->createMock(LoggerInterface::class);

        $outboxHandler = new OutboxEventHandler($outboxStorageMock, $logger);

        $awardCreatedEvent = new AwardCreated(
            aggregateId: Uuid::uuid7()->toString(),
            lotteryId: Uuid::uuid7()->toString(),
            winSum: '1000.09',
            occurredOn: new DateTimeImmutable('now')
        );

        $outboxStorageMock
            ->expects($this->once())
            ->method('createOutbox')
            ->with($this->isInstanceOf(Outbox::class));

        $outboxHandler->addToOutbox($awardCreatedEvent);
    }
}
