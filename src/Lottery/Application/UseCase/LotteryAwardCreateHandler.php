<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Events\DomainEvents\Subscriber\DomainEventSubscriber;
use App\Lottery\Application\Events\EventData\LotteryDeterminedWinner;
use App\Lottery\Application\Exception\LotteryAwardCreateException;
use App\Lottery\Model\LotteryAward;
use App\Lottery\Model\WriteLotteryAward;
use Psr\Log\LoggerInterface;
use Throwable;

final readonly class LotteryAwardCreateHandler
{
    public function __construct(
        private WriteLotteryAward $writeLotteryAward,
        private DomainEventSubscriber $domainEventSubscriber,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(LotteryDeterminedWinner $eventWinner): void
    {
        try {
            $lottery = LotteryAward::createAward(
                event: $eventWinner
            );

            $this->writeLotteryAward->createAward(
                lotteryAward: $lottery
            );

            $this->domainEventSubscriber->handleEvent(...$lottery->pullDomainEvents());
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage()
            );
            throw new LotteryAwardCreateException(
                sprintf('Error from lottery award create %s', $exception->getMessage())
            );
        }
    }
}
