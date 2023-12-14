<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Model\Enum\AwardStatus;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

class LotteryAward
{
    public function __construct(
        #[ORM\Embedded(class: LotteryAwardId::class, columnPrefix: false)]
        private UuidInterface $id,
        #[ORM\Column(type: 'uuid_binary', unique: false)]
        private UuidInterface $lotteryId,
        #[ORM\Column(enumType: AwardStatus::class, type: 'string', nullable: false)]
        private AwardStatus $status,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
        private string $amaountOfWinigs
    ) {
    }
}
