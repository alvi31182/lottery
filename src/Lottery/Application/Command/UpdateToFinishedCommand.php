<?php

declare(strict_types=1);

namespace App\Lottery\Application\Command;

final readonly class UpdateToFinishedCommand
{
    public function __construct(
        public string $lotteryId
    ) {
    }
}
