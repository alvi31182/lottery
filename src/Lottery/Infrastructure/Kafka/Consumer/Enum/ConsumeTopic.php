<?php

declare(strict_types=1);

namespace App\Lottery\Infrastructure\Kafka\Consumer\Enum;

enum ConsumeTopic: string
{
    case PLAYER_DEPOSIT = 'player.v1.deposit';
    case PLAYER_WITHDRAWAL = 'player.v1.withdrawal';
}
