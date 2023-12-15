<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Model\Enum\LotteryStatus;
use App\Lottery\Persistence\Doctrine\LotteryRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use DateTimeImmutable;

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
        private LotteryId $id,
        #[ORM\Column(type: 'uuid', nullable: false)]
        private UuidInterface $playerId,
        #[ORM\Column(type: 'uuid', nullable: false)]
        private UuidInterface $gameId,
        #[ORM\Column(nullable: false, enumType: LotteryStatus::class)]
        private LotteryStatus $status,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
        private string $stake,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: false, updatable: false)]
        private DateTimeImmutable $createdAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $updatedAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $deletedAt,
    ) {
    }

    public static function createStartLottery(string $playerId, string $gameId): self
    {
        return new self(
            id: LotteryId::generateUuidV7(),
            playerId: Uuid::fromString($playerId),
            gameId: Uuid::fromString($gameId),
            status: LotteryStatus::IN_WAITING,
            stake: '12222',
            createdAt: new DateTimeImmutable('now'),
            updatedAt: null,
            deletedAt: null
        );
    }

    public function getId(): LotteryId
    {
        return $this->id;
    }

    public function getPlayerId(): UuidInterface
    {
        return $this->playerId;
    }

    public function getGameId(): UuidInterface
    {
        return $this->gameId;
    }

    public function getStatus(): LotteryStatus
    {
        return $this->status;
    }

    public function getStake(): string
    {
        return $this->stake;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }
}
