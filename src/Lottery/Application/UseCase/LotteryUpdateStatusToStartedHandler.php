<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\UpdateLotteryToStartCommand;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class LotteryUpdateStatusToStartedHandler
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger,
    ) {
    }

    /**
     * @param UpdateLotteryToStartCommand $command
     *
     * @throws LotteryUpdateException
     */
    public function handle(UpdateLotteryToStartCommand $command): void
    {
        try {
            if (empty($command->lotteryList)) {
                $this->logger->info(
                    'Lottery list empty and not yet new lottery started'
                );

                return;
            }

            $this->writeLotteryStorage->updateLotteryStatusToStarted(
                lotteryListWaiting: $command->lotteryList
            );
        } catch (Throwable $exception) {
            $this->handleLotteryUpdateException(exception: $exception);
        }
    }

    /**
     * @throws LotteryUpdateException
     */
    private function handleLotteryUpdateException(Throwable $exception): void
    {
        $logMessage = sprintf(
            'Error updating lottery table: %s, error code: %d, error file: %s, line: %d',
            $exception->getMessage(),
            $exception->getCode(),
            $exception->getFile(),
            $exception->getLine()
        );

        $this->logger->error($logMessage, ['exception' => $exception]);

        throw new LotteryUpdateException(
            message: sprintf('Error from lottery update status to start %s', $exception->getMessage())
        );
    }
}
