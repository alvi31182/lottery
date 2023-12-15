<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto;

final readonly class LotteryList
{
    public function __construct(
        public string $gameId,
        public string $playerId,
        public string $stake
    ) {
    }
}
