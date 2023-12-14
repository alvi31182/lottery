<?php

declare(strict_types=1);

namespace App\Core\Infrastruture\Kafka\Consumer;

use App\Core\Infrastruture\Kafka\Consumer\Enum\ConsumeTopic;
use Psr\Log\LoggerInterface;
use RdKafka\Exception;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicPartition;
use Throwable;

abstract class AbstractKafkaConsumer
{
    protected KafkaConfigForConsumer $kafkaConfigForConsumer;

    public function __construct(
        protected string $dns,
        protected string $consumerGroup,
        protected string $kafkaTopicPrefix,
        protected LoggerInterface $logger
    ) {
        $this->kafkaConfigForConsumer = new KafkaConfigForConsumer(
            dns: $this->dns,
            consumerGroup: $this->consumerGroup,
            kafkaTopicPrefix: $this->kafkaTopicPrefix,
            logger: $this->logger
        );
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    abstract protected function createConsumer(): KafkaConsumer;

    /**
     * @psalm-suppress UndefinedClass
     * @throws Exception
     */
    protected function subscribeToTopics(KafkaConsumer $consumer): void
    {
        $consumer->subscribe([
            $this->kafkaTopicPrefix . ConsumeTopic::PLAYER_DEPOSIT->value,
            $this->kafkaTopicPrefix . ConsumeTopic::PLAYER_WITHDRAWAL->value
        ]);
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    protected function commitMessage(Message $message, KafkaConsumer $consumer): Message
    {
        try {
            if ($message->err === 0) {
                $consumer->commitAsync($message);
            }
        } catch (Throwable $exception) {
            $this->logger->critical(message: 'Async commit KAFKA error', context: [
                'message' => $exception->getMessage(),
            ]);
        } finally {
            try {
                if ($message->err === 0) {
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
