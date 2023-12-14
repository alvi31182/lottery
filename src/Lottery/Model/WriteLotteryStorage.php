<?php

declare(strict_types=1);

namespace App\Lottery\Model;

interface WriteLotteryStorage
{
    public function createLottery(): void;
}
