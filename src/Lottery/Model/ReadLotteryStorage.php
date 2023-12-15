<?php

declare(strict_types=1);

namespace App\Lottery\Model;

interface ReadLotteryStorage
{
    public function findByPlayerWithGameId(string $playerId, string $gameId): ?Lottery;

    public function getLotteryList(): array;
}
