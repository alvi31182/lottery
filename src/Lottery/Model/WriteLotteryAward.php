<?php

declare(strict_types=1);

namespace App\Lottery\Model;

interface WriteLotteryAward
{
    public function createAward(LotteryAward $lotteryAward): void;
}
