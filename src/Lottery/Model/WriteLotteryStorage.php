<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Application\Dto\LotteryList;

interface WriteLotteryStorage
{
    public function createLottery(Lottery $lottery): void;

    /**
     * @param array<LotteryList> $lotteryList
     */
    public function updateLotteryStatusToStarted(array $lotteryList): void;
}
