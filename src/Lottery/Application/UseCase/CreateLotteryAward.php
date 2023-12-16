<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\Award\CreateLotteryAwardCommand;

interface CreateLotteryAward
{
    public function handle(CreateLotteryAwardCommand $command): void;
}
