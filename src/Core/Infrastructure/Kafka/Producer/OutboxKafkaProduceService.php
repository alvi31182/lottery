<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Producer;

use RdKafka\Conf;
use RdKafka\TopicConf;

final class OutboxKafkaProduceService extends AbstractKafkaProducer
{
    protected function buildProducerConfig(): Conf
    {
        $conf = new Conf();
        $conf->set("bootstrap.servers", $this->bootstrapService);
        $conf->set("socket.timeout.ms", "50");
        $conf->set("queue.buffering.max.messages", "1000");
        $conf->set("enable.idempotence", "true");
        $conf->set("max.in.flight.requests.per.connection", "1");

        return $conf;
    }
    protected function buildTopicConf(): TopicConf
    {
        $topicConf = new TopicConf();
        $topicConf->set("message.timeout.ms", "30000");
        $topicConf->set("request.required.acks", "-1");
        $topicConf->set("request.timeout.ms", '60000');

        return $topicConf;
    }

    public function dataForProducer(string $message): int
    {
        return $this->produce($message);
    }
}
