<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto;

final readonly class LotteryListInStarted
{
    /**
     * @param non-empty-string $lotteryId
     * @param non-empty-string $gameId
     * @param non-empty-string $playerId
     * @param non-empty-string $stake
     */
    public function __construct(
        public string $lotteryId,
        public string $gameId,
        public string $playerId,
        public string $stake
    ) {
    }
}
