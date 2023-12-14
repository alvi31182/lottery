<?php

declare(strict_types=1);

namespace App\Core\Infrastruture\Kafka\Consumer;

use App\Core\Infrastruture\Kafka\Settings\Handler\KafkaErrorHandler;
use App\Core\Infrastruture\Kafka\Settings\Handler\KafkaRebalanceHanlder;
use Psr\Log\LoggerInterface;
use RdKafka\Conf;

/**
 * @psalm-suppress UndefinedClass
 */
final readonly class KafkaConfigForConsumer
{
    public function __construct(
        private string $dns,
        private string $consumerGroup,
        private string $kafkaTopicPrefix,
        private LoggerInterface $logger
    ) {
    }

    /**
     * @psalm-suppress UndefinedClass
     */
    public function getConfig(): Conf
    {
        $conf = new Conf();
        $conf->set("metadata.broker.list", $this->dns);
        $conf->set('group.id', $this->kafkaTopicPrefix . $this->consumerGroup);
        $conf->set('enable.auto.commit', 'false');
        $conf->set('enable.auto.offset.store', 'false');
        $conf->set('auto.commit.interval.ms', '1000');
        $conf->set('session.timeout.ms', '36000');

        $errorHandler = new KafkaErrorHandler($this->logger);
        $conf->setErrorCb([$errorHandler, 'handleError']);

        $rebalancedHandler = new KafkaRebalanceHanlder($this->logger);
        $conf->setRebalanceCb([$rebalancedHandler, 'handleRebalanceCb']);

        return $conf;
    }
}
