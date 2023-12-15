<?php

declare(strict_types=1);

namespace App\Lottery\Application\Command;

final readonly class CreateLotteryCommand
{
    public function __construct(
        public string $playerId,
        public string $gameId,
        public string $stake
    ) {
    }
}
