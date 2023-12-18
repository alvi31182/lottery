<?php

declare(strict_types=1);

namespace App\Lottery\Application\Event\Subscriber;

use App\Lottery\Application\Event\EventData\LotteryStatusUpdated;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Application\UseCase\LotteryUpdateStatusToStartedHandler;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

readonly class LotteryEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private LotteryUpdateStatusToStartedHandler $handler
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
     * @throws LotteryUpdateException
     */
    public function process(LotteryStatusUpdated $event): void
    {
        $this->handler->handle(lotteryStatusUpdatedEvent: $event);
    }
}
