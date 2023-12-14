<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Settings\Handler;

use Exception;
use Psr\Log\LoggerInterface;
use RdKafka\KafkaConsumer;
use RdKafka\TopicPartition;

final readonly class KafkaRebalanceHanlder
{
    public function __construct(private LoggerInterface $logger)
    {
    }

    /**
     * @psalm-suppress UndefinedClass
     * @psalm-suppress UndefinedConstant
     * @throws \RdKafka\Exception
     * @throws Exception
     */
    public function handleRebalanceCb(
        KafkaConsumer $kafka,
        string $err,
        mixed $reason,
        ?array $partitions = null
    ): void {
        if ($partitions !== null) {
            switch ($err) {
                case RD_KAFKA_RESP_ERR__ASSIGN_PARTITIONS:
                    /** @var TopicPartition $topicPartition */
                    foreach ($partitions as $topicPartition) {
                        $this->logger->info(
                            sprintf(
                                'Assign: %s %s %s',
                                (string) $topicPartition->getTopic(),
                                (int) $topicPartition->getPartition(),
                                (string) $topicPartition->getOffset(),
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
                                'Assign: %s %s %s',
                                (string) $topicPartition->getTopic(),
                                (int) $topicPartition->getPartition(),
                                (string) $topicPartition->getOffset(),
                            ),
                        );
                    }
                    $kafka->assign(null);
                    break;

                default:
                    throw new Exception($err);
            }
        }
    }
}
