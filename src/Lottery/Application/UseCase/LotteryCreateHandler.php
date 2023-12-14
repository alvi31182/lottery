<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Dto\Request\StartLotteryRequest;
use App\Lottery\Model\ReadLotteryStorage;

final class LotteryCreateHandler
{
    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage
    ) {
    }
    public function handler(StartLotteryRequest $request): void
    {
        $isNotExistsLotteryAndGameById = $this->readLotteryStorage->getByPlayerWithGameId(
            playerId: $request->playerId
        );

        dd($isNotExistsLotteryAndGameById);
    }
}
