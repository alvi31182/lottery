<?php

declare(strict_types=1);

namespace App\Lottery\Application\Event\Subscriber;

use App\Lottery\Application\Event\LotteryStatusUpdated;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

readonly class LotteryEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @return array{
     *      "lottery_status.updated": array{
     *          0: array{0: string, 1: int}
     *      }
     *  }
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LotteryStatusUpdated::NAME => [
                [
                    'process',
                    10,
                ],
            ],
        ];
    }

    /**
     * @param LotteryStatusUpdated $event
     *
     * @throws LotteryUpdateException
     */
    public function process(LotteryStatusUpdated $event): void
    {
        try {
            $this->writeLotteryStorage->updateLotteryStatusToStarted(
                lotteryListWaiting: $event->getLotteryList()
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
