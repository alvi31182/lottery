<?php

declare(strict_types=1);

namespace App\Lottery\Application\Dto\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class StartLotteryRequest
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Uuid(message: 'The value {{ value }} is not a valid UUID.')]
        public string $playerId,
        #[Assert\NotBlank]
        #[Assert\Uuid(message: 'The value {{ value }} is not a valid UUID.')]
        public string $gameId,
    ) {
    }
}
