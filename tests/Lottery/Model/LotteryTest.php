<?php

declare(strict_types=1);

namespace Lottery\Model;

use App\Lottery\Application\Command\CreateLotteryCommand;
use App\Lottery\Model\Enum\LotteryStatus;
use App\Lottery\Model\Lottery;
use App\Lottery\Model\LotteryId;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class LotteryTest extends TestCase
{
    public function testCreateStartLottery(): void
    {

        $command = new CreateLotteryCommand(
            playerId: Uuid::uuid7()->toString(),
            gameId: Uuid::uuid7()->toString(),
            stake: '10.00'
        );


        $lottery = Lottery::createStartLottery($command);


        $this->assertInstanceOf(LotteryId::class, $lottery->getId());

        $this->assertSame(LotteryStatus::IN_WAITING, $lottery->getStatus());
    }
}
