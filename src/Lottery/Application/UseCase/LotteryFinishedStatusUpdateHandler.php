<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\UpdateToFinishedCommand;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Model\LotteryId;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Throwable;

final readonly class LotteryFinishedStatusUpdateHandler
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws LotteryUpdateException
     */
    public function handle(UpdateToFinishedCommand $command): void
    {
        try {
            $this->writeLotteryStorage->updateLotteryStatusToFinished(
                lotteryId: new LotteryId(id: Uuid::fromString($command->lotteryId))
            );
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage()
            );
            throw new LotteryUpdateException(
                sprintf(
                    'Error from update lottery status to finished %s %d',
                    $exception->getMessage(),
                    $exception->getCode()
                )
            );
        }
    }
}
