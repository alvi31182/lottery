<?php

declare(strict_types=1);

namespace App\Lottery\Application\Command;

final readonly class UpdateLotteryToStartCommand
{
    public function __construct(
        public array $lotteryList
    ) {
    }
}
