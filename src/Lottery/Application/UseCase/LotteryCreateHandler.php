<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\CreateLotteryCommand;
use App\Lottery\Model\Lottery;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class LotteryCreateHandler
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger,
    ) {
    }

    public function handle(string $message): void
    {
        $messageData = json_decode($message, true, JSON_THROW_ON_ERROR);

        try {
            $lottery = Lottery::createStartLottery(
                new CreateLotteryCommand(
                    playerId: $messageData['game']['playerId'],
                    gameId: $messageData['game']['gameId'],
                    stake: $messageData['game']['stake']
                )
            );

            $this->writeLotteryStorage->createLottery($lottery);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage(),
                context: [
                    'trace' => $exception->getTrace(),
                ]
            );
        }
    }
}
