<?php

declare(strict_types=1);

namespace App\Lottery\Model;

interface ReadLotteryStorage
{
    public function getLotteryList(): array;
}
