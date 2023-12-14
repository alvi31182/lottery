<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Kafka\Consumer;

use RdKafka\Exception;
use RdKafka\KafkaConsumer;

final class KafkaConsumerService extends AbstractKafkaConsumer
{
    /**
     * @psalm-suppress UndefinedClass
     */
    protected function createConsumer(): KafkaConsumer
    {
        $config = $this->kafkaConfigForConsumer;
        return new KafkaConsumer($config->getConfig());
    }

    /**
     * @psalm-suppress UndefinedClass
     * @throws Exception
     */
    public function consumeFromKafka(): KafkaConsumer
    {
        $consumer = $this->createConsumer();
        $this->subscribeToTopics($consumer);
        return $consumer;
    }
}
