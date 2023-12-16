<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto;

final readonly class LotteryListInStarted
{
    public function __construct(
        public string $lotteryId,
        public string $gameId,
        public string $playerId,
        public string $stake
    ) {
    }
}
