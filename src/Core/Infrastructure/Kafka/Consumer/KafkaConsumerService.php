<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Consumer;

use App\Core\Infrastructure\Kafka\Consumer\Enum\ConsumeTopic;
use App\Core\Infrastructure\Kafka\Settings\Handler\KafkaErrorHandler;
use App\Core\Infrastructure\Kafka\Settings\Handler\KafkaRebalanceHanlder;
use RdKafka\Conf;
use RdKafka\Exception;
use RdKafka\KafkaConsumer;
use RdKafka\Message;

final class KafkaConsumerService extends AbstractKafkaConsumer implements KafkaMessageConsumer
{
    /**
     * @psalm-suppress UndefinedClass
     */
    protected function buildConsumerConfig(): Conf
    {
        $conf = new Conf();
        $conf->set("metadata.broker.list", $this->dns);
        $conf->set('group.id', $this->kafkaTopicPrefix . $this->consumerGroup);
        $conf->set('enable.partition.eof', 'true');
        $conf->set('auto.offset.reset', 'earliest');

        return $conf;
    }

    /**
     * @throws Exception
     * @psalm-suppress UndefinedClass
     */
    protected function kafkaConsumer(): KafkaConsumer
    {
        $conf = $this->buildConsumerConfig();
        $errorHandler = new KafkaErrorHandler($this->logger);
        $conf->setErrorCb([$errorHandler, 'handleError']);

        $rebalancedHandler = new KafkaRebalanceHanlder($this->logger);
        $conf->setRebalanceCb([$rebalancedHandler, 'handleRebalanceCb']);

        $consumer = new KafkaConsumer(
            $conf
        );

        $consumer->subscribe(topics: $this->subscribeToTopics());

        return $consumer;
    }

    protected function subscribeToTopics(): array
    {
        return [
            $this->kafkaTopicPrefix . ConsumeTopic::PLAYER_STAKED->value
        ];
    }

    /**
     * @psalm-suppress UndefinedClass
     * @throws Exception
     */
    public function consumeFromKafka(): Message
    {
        $consumer = $this->kafkaConsumer();

        $msg = $consumer->consume(timeout_ms: 2_000);
        dump($msg->payload);
        $message = $this->commitMessage(
            message: $msg,
            consumer: $consumer
        );
      //  dump($message->payload);

        return $message;
    }
}
