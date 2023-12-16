<?php

declare(strict_types=1);

namespace App\Lottery\Application\Command\Award;

final readonly class CreateLotteryAwardCommand
{
    public function __construct(
        public string $winSum,
        public string $lotteryId,
    ) {
    }
}
