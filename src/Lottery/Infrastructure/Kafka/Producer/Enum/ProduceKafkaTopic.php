<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Kafka\Producer\Enum;

enum ProduceKafkaTopic: string
{
    case LOTTERY_STARTED = 'lottery.v1.started';
    case LOTTERY_FINISHED = 'lottery.v1.finished';
}
