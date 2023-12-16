<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Application\Dto\LotteryListInWaiting;

interface WriteLotteryStorage
{
    public function createLottery(Lottery $lottery): void;

    /**
     * @param array<LotteryListInWaiting> $lotteryListWaiting
     */
    public function updateLotteryStatusToStarted(array $lotteryListWaiting): void;
}
