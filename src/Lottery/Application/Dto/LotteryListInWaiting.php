<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto;

final readonly class LotteryListInWaiting
{
    public function __construct(
        public string $gameId,
        public string $playerId
    ) {
    }
}
