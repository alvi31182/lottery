<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Consumer;

use Psr\Log\LoggerInterface;
use RdKafka\Conf;
use RdKafka\Exception;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicPartition;
use Throwable;

abstract class AbstractKafkaConsumer implements KafkaMessageConsumer
{
    public function __construct(
        protected string $dns,
        protected string $consumerGroup,
        protected string $kafkaTopicPrefix,
        protected LoggerInterface $logger
    ) {
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    abstract protected function buildConsumerConfig(): Conf;

    /**
     * @psalm-suppress  UndefinedClass
     */
    abstract protected function kafkaConsumer(): KafkaConsumer;

    /**
     * @psalm-suppress UndefinedClass
     *
     * @return array<string>
     *
     * @throws Exception
     */
    abstract protected function subscribeToTopics(): array;

    /**
     * @psalm-suppress UndefinedClass
     */
    protected function commitMessage(Message $message, KafkaConsumer $consumer): Message
    {
        try {
            if (0 === $message->err) {
                $consumer->commitAsync($message);
            }
        } catch (Throwable $exception) {
            $this->logger->critical(message: 'Async commit KAFKA error', context: [
                'message' => $exception->getMessage(),
            ]);
        } finally {
            try {
                if (0 === $message->err) {
                    $topic = $consumer->newTopic(topic_name: $message->topic_name);
                    $part = new TopicPartition($message->topic_name, $message->partition, $message->offset);

                    $topic->offsetStore(partition: $part->getPartition(), offset: $part->getOffset());
                    $consumer->commit($message);
                }
            } catch (Throwable $exception) {
                $this->logger->critical(message: 'Sync commit KAFKA error', context: [
                    'message' => $exception->getMessage(),
                ]);
            }
        }

        return $message;
    }
}
