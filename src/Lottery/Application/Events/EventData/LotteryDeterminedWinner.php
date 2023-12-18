<?php

declare(strict_types=1);

namespace App\Lottery\Application\Events\EventData;

final readonly class LotteryDeterminedWinner
{
    public const NAME = 'lottery_determinate.winner';

    public function __construct(
        public string $winSum,
        public string $lotteryId,
    ) {
    }
}
