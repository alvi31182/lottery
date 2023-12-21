<?php

declare(strict_types=1);

namespace App\Lottery\Application\Events\Subscriber;

use App\Lottery\Application\Events\EventData\LotteryDeterminedWinner;
use App\Lottery\Application\UseCase\LotteryAwardCreateHandler;
use Exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class LotteryAwardEventSubscriber implements EventSubscriberInterface
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

    /**
     * @throws Exception
     */
    public function createWinner(LotteryDeterminedWinner $eventWinner): void
    {
        $this->handler->handle(eventWinner: $eventWinner);
    }
}
