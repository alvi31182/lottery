<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Repository\Doctrine\LotteryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(repositoryClass: LotteryRepository::class)]
class Lottery
{
    public function __construct(
        #[ORM\Embedded(class: LotteryId::class)]
        private LotteryId $id,
        #[ORM\Column(type: 'uuid', nullable: false)]
        private UuidInterface $playerId,
        #[ORM\Column(type: 'string', nullable: false)]
        private string $status,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: false)]
        private DateTimeImmutable $createdAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $updatedAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $deletedAt,
    ) {
        $this->createdAt = new DateTimeImmutable('now');
    }
}
