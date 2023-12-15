<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Model\WriteLotteryStorage;

final readonly class UpdateLotteryStatusHandler implements UpdateLotteryStatus
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage
    ) {
    }

    public function updateStatus(): void
    {
    }
}
