<?php

declare(strict_types=1);

namespace App\Lottery\Model\Enum;

enum LotteryStatus: string
{
    case IN_WAITING = 'in_waiting';
    case STARTED = 'started';
    case FINISHED = 'finished';
}
