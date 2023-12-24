<?php

declare(strict_types=1);

namespace App\Lottery\Model\Enum;

enum AwardStatus: string
{
    case PLAYED_OUT = 'played_out';
}
