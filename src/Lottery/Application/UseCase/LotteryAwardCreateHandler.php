<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\Award\CreateLotteryAwardCommand;
use App\Lottery\Model\LotteryAward;
use App\Lottery\Model\WriteLotteryAward;

final readonly class LotteryAwardCreateHandler implements CreateLotteryAward
{
    public function __construct(
        private WriteLotteryAward $writeLotteryAward
    ) {
    }
    public function handle(CreateLotteryAwardCommand $command): void
    {
        $this->writeLotteryAward->createAward(
            lotteryAward: LotteryAward::createAward(
                command: $command
            )
        );
    }
}
