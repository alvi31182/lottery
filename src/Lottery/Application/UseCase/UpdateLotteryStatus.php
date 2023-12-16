<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Dto\LotteryListInWaiting;

interface UpdateLotteryStatus
{
    /**
     * @param array<LotteryListInWaiting> $lotteryList
     */
    public function updateStatus(array $lotteryList): void;
}
