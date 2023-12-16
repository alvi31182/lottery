<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\Dto\LotteryListInWaiting;

interface ReadLotteryStorage
{
    public function findByPlayerWithGameId(string $playerId, string $gameId): ?Lottery;

    /**
     * @return array<LotteryListInWaiting>
     */
    public function getLotteryListByStatusInWaiting(): array;

    /**
     * @return array<LotteryListInStarted>
     */
    public function getLotteryListInStarted(): array;
}
