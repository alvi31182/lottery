<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Dto\Request\StartLotteryRequest;
use App\Lottery\Application\Exception\LotteryNotFoundException;
use App\Lottery\Model\ReadLotteryStorage;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class LotteryCreateHandler
{
    public function __construct(
        private ReadLotteryStorage $readLotteryStorage,
        private LoggerInterface $logger
    ) {
    }

    public function handler(StartLotteryRequest $request): void
    {

        try {
            $lottery = $this->readLotteryStorage->findByPlayerWithGameId(
                playerId: $request->playerId,
                gameId: $request->gameId
            );

            if ($lottery === null) {
                throw new LotteryNotFoundException('Lottery not found by playerId');
            }

            $lottery->createStartLottery(
            );
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage()
            );
        }
    }
}
