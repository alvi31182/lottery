<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto;

final readonly class LotteryListInWaiting
{
    /**
     * @param non-empty-string $gameId
     * @param non-empty-string $playerId
     */
    public function __construct(
        public string $gameId,
        public string $playerId
    ) {
    }
}
