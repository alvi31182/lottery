<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Dto\LotteryList;

interface UpdateLotteryStatus
{
    /**
     * @param array<LotteryList> $lotteryList
     */
    public function updateStatus(array $lotteryList): void;
}
