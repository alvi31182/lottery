<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Producer;

use App\Core\Infrastructure\Kafka\Producer\Enum\ProduceKafkaTopic;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Nonstandard\Uuid;
use RdKafka\Conf;
use RdKafka\Exception;
use RdKafka\Producer;
use RdKafka\TopicConf;

abstract class AbstractKafkaProducer
{
    public function __construct(
        protected string $kafkaPrefixTopic,
        protected string $bootstrapService,
        protected LoggerInterface $logger
    ) {
    }

    abstract protected function buildProducerConfig(): Conf;

    abstract protected function buildTopicConf(): TopicConf;

    /**
     * @throws Exception
     */
    public function produce(string $message): int
    {
        $producer = $this->producer();
        $topicConf = $this->buildTopicConf();

        $topic = $producer->newTopic(
            topic_name: $this->kafkaPrefixTopic . ProduceKafkaTopic::LOTTERY_AWARD->value,
            topic_conf: $topicConf,
        );

        $topic->producev(
            partition: RD_KAFKA_PARTITION_UA,
            msgflags: 0,
            payload: $message,
            headers: [
                'uuid' => Uuid::uuid7()->toString(),
            ],
        );

        $producer->poll(timeout_ms: 2_000);
        $result = $producer->flush(5_000);

        if ($result !== RD_KAFKA_RESP_ERR_NO_ERROR) {
            $this->logger->critical(
                message: sprintf("ERROR FROM PRODUCER KAFKA ERROR CODE: %d", $result),
            );
        }

        return $result;
    }

    protected function producer(): Producer
    {
        $producer = new Producer($this->buildProducerConfig());
        $producer->addBrokers("kafka");
        return $producer;
    }
}
