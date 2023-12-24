<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Producer\Enum;

enum ProduceKafkaTopic: string
{
    case LOTTERY_AWARD = 'lottery.v1.award';
}
