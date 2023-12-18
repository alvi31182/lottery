<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Event\EventData\LotteryDeterminedWinner;
use App\Lottery\Model\LotteryAward;
use App\Lottery\Model\WriteLotteryAward;

final readonly class LotteryAwardCreateHandler
{
    public function __construct(
        private WriteLotteryAward $writeLotteryAward
    ) {
    }
    public function handle(LotteryDeterminedWinner $eventWinner): void
    {
        $this->writeLotteryAward->createAward(
            lotteryAward: LotteryAward::createAward(
                event: $eventWinner
            )
        );
    }
}
