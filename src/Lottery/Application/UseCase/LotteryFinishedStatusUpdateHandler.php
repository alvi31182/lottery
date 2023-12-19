<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\UpdateToFinishedCommand;
use App\Lottery\Model\LotteryId;
use App\Lottery\Model\WriteLotteryStorage;
use Ramsey\Uuid\Uuid;

final readonly class LotteryFinishedStatusUpdateHandler
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage
    ) {
    }

    public function handle(UpdateToFinishedCommand $command): void
    {
        $this->writeLotteryStorage->updateLotteryStatusToFinished(
            lotteryId: new LotteryId(id: Uuid::fromString($command->lotteryId))
        );
    }
}
