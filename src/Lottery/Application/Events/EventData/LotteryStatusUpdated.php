<?php

declare(strict_types=1);

namespace App\Lottery\Application\Events\EventData;

use App\Lottery\Application\Dto\LotteryListInStarted;

final class LotteryStatusUpdated
{
    public const NAME = 'lottery_status.updated';

    /**
     * @param array<LotteryListInStarted> $lotteryListStared
     */
    public function __construct(
        public readonly array $lotteryListStared
    ) {
    }
}
