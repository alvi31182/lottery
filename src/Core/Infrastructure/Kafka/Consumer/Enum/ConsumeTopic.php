<?php

declare(strict_types=1);

namespace App\Core\Infrastructure\Kafka\Consumer\Enum;

enum ConsumeTopic: string
{
    case PLAYER_STAKED = 'player.v1.staked';
}
