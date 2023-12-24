<?php

declare(strict_types=1);

namespace App\Outbox\Application\UseCase;

use App\Core\Infrastructure\Kafka\Producer\OutboxKafkaProduceService;
use App\Outbox\Application\Console\Scheduler\OutboxSignalMessage;
use App\Outbox\Application\Exception\OutboxCreateException;
use App\Outbox\Application\Exception\OutboxGetListException;
use App\Outbox\Model\ReadOutboxStorage;
use App\Outbox\Model\WriteOutboxStorage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Throwable;

#[AsMessageHandler]
final readonly class OutboxSchedulerMessageHandler
{
    public function __construct(
        private ReadOutboxStorage $readOutboxStorage,
        private WriteOutboxStorage $writeOutboxStorage,
        private OutboxKafkaProduceService $producer,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @psalm-suppress UndefinedConstant
     *
     * @throws OutboxGetListException
     */
    public function __invoke(OutboxSignalMessage $signal): void
    {
        try {
            $outboxList = $this->readOutboxStorage->getNotSendOutboxData();

            foreach ($outboxList as $outbox) {
                if ($outbox->isSend === false) {
                    $result = $this->producer->dataForProducer($outbox->eventData);

                    if ($result === RD_KAFKA_RESP_ERR_NO_ERROR) {
                        $this->updateOutboxIsSendToTrue(outboxId: $outbox->outboxId);
                    }
                }
            }
        } catch (Throwable $exception) {
            $this->logger->critical(
                message: $exception->getMessage()
            );
            throw new OutboxGetListException(
                sprintf('Outbox get list error %s', $exception->getMessage())
            );
        }
    }

    /**
     * @throws OutboxCreateException
     */
    public function updateOutboxIsSendToTrue(string $outboxId): void
    {
        try {
            $outbox = $this->readOutboxStorage->findById(id: $outboxId);

            if ($outbox !== null) {
                $this->writeOutboxStorage->statusSendToTrue(
                    outbox: $outbox->statusIsSendToTrue()
                );
            }
        } catch (Throwable $exception) {
            $this->logger->critical(
                message: $exception->getMessage()
            );
            throw new OutboxCreateException(
                sprintf('Outbox get list error %s', $exception->getMessage())
            );
        }
    }
}
