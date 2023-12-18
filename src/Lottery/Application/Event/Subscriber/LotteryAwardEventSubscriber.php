<?php

declare(strict_types=1);

namespace App\Lottery\Application\Event\Subscriber;

use App\Lottery\Application\Event\EventData\LotteryDeterminedWinner;
use App\Lottery\Application\UseCase\LotteryAwardCreateHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class LotteryAwardEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LotteryAwardCreateHandler $handler
    ) {
    }

    /**
     * @return array{
     *     "lottery_determinate.winner": array{
     *         0: array{0: string, 1: int}
     *     }
     * }
     */
    public static function getSubscribedEvents(): array
    {
        return [
            LotteryDeterminedWinner::NAME => [
                [
                    'createWinner',
                    11,
                ],
            ],
        ];
    }

    public function createWinner(LotteryDeterminedWinner $eventWinner): void
    {
        $this->handler->handle(eventWinner: $eventWinner);
    }
}
