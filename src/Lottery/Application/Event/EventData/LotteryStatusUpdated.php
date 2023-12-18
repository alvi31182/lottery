<?php

declare(strict_types=1);

namespace App\Lottery\Application\Event\EventData;

use App\Lottery\Application\Dto\LotteryListInWaiting;

final class LotteryStatusUpdated
{
    public const NAME = 'lottery_status.updated';

    /**
     * @param array<LotteryListInWaiting> $lotteryList
     */
    public function __construct(
        private readonly array $lotteryList
    ) {
    }

    /**
     * @return array<LotteryListInWaiting>
     */
    public function getLotteryList(): array
    {
        return $this->lotteryList;
    }
}
