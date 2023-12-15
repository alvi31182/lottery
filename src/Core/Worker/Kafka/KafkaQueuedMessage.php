<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use RdKafka\Message;

final readonly class KafkaQueuedMessage
{
    /**
     * @psalm-suppress UndefinedClass
     */
    public function __construct(
        private Message $message,
        private int $serialNumber
    ) {
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    public function getMessage(): Message
    {
        return $this->message;
    }

    public function getSerialNumber(): int
    {
        return $this->serialNumber;
    }
}
