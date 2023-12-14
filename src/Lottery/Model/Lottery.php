<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Model\Enum\LotteryStatus;
use App\Lottery\Persistence\Doctrine\LotteryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: LotteryRepository::class)]
#[Index(
    columns: ['id'],
    name: 'btree_lottery_idx',
    options: ['using' => 'btree']
)]
class Lottery
{
    public function __construct(
        #[ORM\Embedded(class: LotteryId::class, columnPrefix: false)]
        private UuidInterface $id,
        #[ORM\Column(type: 'uuid', nullable: false)]
        private UuidInterface $playerId,
        #[ORM\Column(type: 'uuid', nullable: false)]
        private UuidInterface $gameId,
        #[ORM\Column(enumType: LotteryStatus::class, nullable: false)]
        private LotteryStatus $status,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: false, updatable: false)]
        private DateTimeImmutable $createdAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $updatedAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $deletedAt,
    ) {
    }

    public function createStartLottery(): self
    {
        return new self(
            id: LotteryId::generateUuidV7(),
            playerId: LotteryId::generateUuidV7(),
            gameId: LotteryId::generateUuidV7(),
            status: LotteryStatus::IN_WAITING,
            createdAt: new DateTimeImmutable('now'),
            updatedAt: null,
            deletedAt: null
        );
    }
}
