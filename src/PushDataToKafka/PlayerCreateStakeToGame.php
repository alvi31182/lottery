<?php

declare(strict_types=1);

namespace App\PushDataToKafka;

use Ramsey\Uuid\Uuid;
use RdKafka\Conf;
use RdKafka\Producer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/stake', methods: ['POST'])]
final class PlayerCreateStakeToGame extends AbstractController
{
    private const TOPIC_NAME = 'player.v1.staked';
    private const TOPIC_PREFIX = 'lottery_';

    public function __construct()
    {
    }

    /**
     * @psalm-suppress UndefinedClass
     * @psalm-suppress UndefinedConstant
     *
     * @return JsonResponse
     * @throws \JsonException
     * @throws \RdKafka\Exception
     */
    public function __invoke(): JsonResponse
    {
        $conf = new Conf();
        $conf->set('log_level', (string)LOG_DEBUG);
        $conf->set('debug', 'topic');
        $conf->set('socket.timeout.ms', "50");
        $conf->set('queue.buffering.max.messages', "1000");

        $rk = new Producer($conf);
        $rk->addBrokers("lottery_kafka");

        $data = [
            'message' => [
                'eventType' => 'player.stakeCreated',
            ],
            'game' => [
                'playerId' => Uuid::uuid7()->toString(),
                'gameId' => Uuid::uuid7()->toString(),
                'stake' => (string)rand(1000, 20000)
            ]
        ];

        $payload = json_encode($data, JSON_THROW_ON_ERROR | JSON_FORCE_OBJECT);

        $topic = $rk->newTopic(self::TOPIC_PREFIX . self::TOPIC_NAME);
        $topic->producev(
            partition: RD_KAFKA_PARTITION_UA,
            msgflags: 0,
            payload: $payload,
            headers: ['uuid' => Uuid::uuid7()->toString()],
        );

        $rk->poll(2000);

        return new JsonResponse([
            'success' => 'topic created'
        ]);
    }
}
