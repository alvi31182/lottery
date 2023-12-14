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
     * @throws Exception
     */
    abstract protected function subscribeToTopics(): array;
//    {
//      //  dd($consumer->)
////        $conf = new Conf();
////        $conf->set("metadata.broker.list", $this->dns);
////        $conf->set('group.id', $this->kafkaTopicPrefix . $this->consumerGroup);
////        $conf->set('enable.auto.commit', 'false');
////        $conf->set('enable.auto.offset.store', 'false');
////        $conf->set('auto.commit.interval.ms', '1000');
////        $conf->set('session.timeout.ms', '36000');
////        $conf->set('enable.partition.eof', 'true');
////
////        $consumer = new KafkaConsumer($conf);
//
//        $consumer->subscribe([
//            $this->kafkaTopicPrefix . ConsumeTopic::PLAYER_STAKED->value
//        ]);
//
//        return $consumer;
//    }

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
