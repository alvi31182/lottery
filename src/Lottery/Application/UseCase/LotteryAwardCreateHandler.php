<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Events\DomainEvents\Subscriber\DomainEventSubscriber;
use App\Lottery\Application\Events\EventData\LotteryDeterminedWinner;
use App\Lottery\Model\LotteryAward;
use App\Lottery\Model\WriteLotteryAward;

final readonly class LotteryAwardCreateHandler
{
    public function __construct(
        private WriteLotteryAward $writeLotteryAward,
        private DomainEventSubscriber $domainEventSubscriber
    ) {
    }

    /**
     * @throws \Exception
     */
    public function handle(LotteryDeterminedWinner $eventWinner): void
    {
        $lottery = LotteryAward::createAward(
            event: $eventWinner
        );

        $this->writeLotteryAward->createAward(
            lotteryAward: $lottery
        );

        $this->domainEventSubscriber->handleEvent(...$lottery->pullDomainEvents());
    }
}
