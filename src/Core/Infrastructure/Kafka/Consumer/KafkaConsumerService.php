<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Consumer;

use App\Core\Infrastructure\Kafka\Consumer\Enum\ConsumeTopic;
use RdKafka\Conf;
use RdKafka\KafkaConsumer;
use RdKafka\Message;
use RdKafka\TopicPartition;
use Exception;
use Throwable;

final class KafkaConsumerService extends AbstractKafkaConsumer implements KafkaMessageConsumer
{
    /**
     * @psalm-suppress UndefinedConstant
     * @psalm-suppress UndefinedFunction
     */
    protected function buildConsumerConfig(): Conf
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $this->dns);
        $conf->set('group.id', $this->kafkaTopicPrefix . $this->consumerGroup);
        $conf->set('enable.auto.commit', 'false');
        $conf->set('enable.auto.offset.store', 'false');
        $conf->set('auto.commit.interval.ms', '1000');
        $conf->set('session.timeout.ms', '36000');
        $conf->set('enable.partition.eof', 'true');
        $conf->setErrorCb(
            function (mixed $error, string $reason): void {
                if (RD_KAFKA_RESP_ERR__FATAL === $error) {
                    $this->logger->critical(
                        message: 'KAFKA FATAL ERROR',
                        context: [
                            'message' => sprintf('Error %d %s. Reason: %s', $error, rd_kafka_err2str($error), $reason),
                        ],
                    );
                }
            },
        );

        /* @psalm-suppress UndefinedConstant */
        $conf->setRebalanceCb(function (KafkaConsumer $kafka, string $err, array $partitions = null): void {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    /** @var TopicPartition $topicPartition */
                    foreach ($partitions as $topicPartition) {
                        $this->logger->info(
                            sprintf(
                                'Assign: %s %d %d',
                                $topicPartition->getTopic(),
                                $topicPartition->getPartition(),
                                $topicPartition->getOffset(),
                            ),
                        );
                    }

                    $kafka->assign($partitions);
                    break;

                case RD_KAFKA_RESP_ERR__REVOKE_PARTITIONS:
                    /** @var TopicPartition $topicPartition */
                    foreach ($partitions as $topicPartition) {
                        $this->logger->info(
                            sprintf(
                                'Assign: %s %d %d',
                                $topicPartition->getTopic(),
                                $topicPartition->getPartition(),
                                $topicPartition->getOffset(),
                            ),
                        );
                    }
                    $kafka->assign(null);
                    break;
                default:
                    throw new Exception($err);
            }
        });

        return $conf;
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    protected function kafkaConsumer(): KafkaConsumer
    {
        return new KafkaConsumer(
            $this->buildConsumerConfig()
        );
    }

    /**
     * @return array<string>
     */
    protected function subscribeToTopics(): array
    {
        return [
            $this->kafkaTopicPrefix . ConsumeTopic::PLAYER_STAKED->value,
        ];
    }

    /**
     * @throws \Exception
     * @throws \Throwable
     */
    public function consumeFromKafka(): Message
    {
        try {
            $consumer = $this->kafkaConsumer();
            $consumer->subscribe(topics: $this->subscribeToTopics());
            $message = $consumer->consume(2_000);
            $this->commitMessage($message, $consumer);

            return $message;
        } catch (Throwable $exception) {
            $this->logger->error(
                message: "Exception in Kafka consumer: {$exception->getMessage()}"
            );
            throw $exception;
        }
    }
}
