<?php

declare(strict_types=1);

namespace App\Outbox\Application\UseCase;

use App\Core\Infrastructure\Kafka\Producer\OutboxKafkaProduceService;
use App\Outbox\Application\Console\Scheduler\OutboxSignalMessage;
use App\Outbox\Model\ReadOutboxStorage;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class OutboxSchedulerMessageHandler
{
    public function __construct(
        private ReadOutboxStorage $readOutboxStorage,
        private OutboxKafkaProduceService $producer
    ) {
    }

    public function __invoke(OutboxSignalMessage $signal): void
    {
        $dataForKafka = $this->readOutboxStorage->getNotSendOutboxData();

        /** @var array<string> $message */
        foreach ($dataForKafka as $message) {
            $this->producer->dataForProducer($message['event_data']);
        }
    }
}
