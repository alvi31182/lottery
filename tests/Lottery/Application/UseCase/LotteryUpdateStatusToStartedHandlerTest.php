<?php

declare(strict_types=1);

namespace Lottery\Application\UseCase;

use App\Lottery\Application\Dto\LotteryListInWaiting;
use App\Lottery\Application\Events\EventData\LotteryStatusUpdated;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Application\UseCase\LotteryUpdateStatusToStartedHandler;
use App\Lottery\Model\WriteLotteryStorage;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;

class LotteryUpdateStatusToStartedHandlerTest extends TestCase
{
    /**
     * @throws LotteryUpdateException
     * @throws Exception
     */
    public function testHandle(): void
    {
        $this->markTestSkipped();
        $lotteryList = [
            new LotteryListInWaiting(
                gameId: Uuid::uuid7()->toString(),
                playerId: Uuid::uuid7()->toString()
            ),
            new LotteryListInWaiting(
                gameId: Uuid::uuid7()->toString(),
                playerId: Uuid::uuid7()->toString()
            ),
        ];

        $writeLotteryStorageMock = $this->createPartialMock(
            WriteLotteryStorage::class,
            ['updateLotteryStatusToStarted', 'createLottery']
        );
        $writeLotteryStorageMock
            ->expects($this->once())
            ->method('updateLotteryStatusToStarted')
            ->with($lotteryList)
        ;

        $loggerMock = $this->createMock(LoggerInterface::class);

        $event = new LotteryStatusUpdated($lotteryList);

        $handler = new LotteryUpdateStatusToStartedHandler($writeLotteryStorageMock, $loggerMock);

        $handler->handle($event);
    }
}
