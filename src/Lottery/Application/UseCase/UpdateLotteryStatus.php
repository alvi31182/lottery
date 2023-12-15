<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

interface UpdateLotteryStatus
{
    public function updateStatus(): void;
}
