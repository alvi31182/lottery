<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Exception\LotteryWriteException;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class UpdateLotteryStatusHandler implements UpdateLotteryStatus
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws LotteryWriteException
     */
    public function updateStatus(array $lotteryList): void
    {
        try {
            $this->writeLotteryStorage->updateLotteryStatusToStarted(lotteryListWaiting: $lotteryList);
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage(),
                context: [
                    'code' => $exception->getCode(),
                    'trace' => $exception->getTraceAsString(),
                ]
            );
            throw new LotteryWriteException(
                message: sprintf(
                    'Error update lottery table %s, error code %d, error file %s, line %d',
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception->getFile(),
                    $exception->getLine()
                ),
            );
        }
    }
}
