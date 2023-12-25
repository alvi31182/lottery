<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use App\Lottery\Application\Events\EventData\LotteryDeterminedWinner;
use App\Lottery\Infrastructure\Persistence\Doctrine\LotteryAwardRepository;
use App\Lottery\Model\Enum\AwardStatus;
use App\Lottery\Model\Events\AwardCreated;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Index;
use Ramsey\Uuid\Uuid;

#[ORM\Entity(repositoryClass: LotteryAwardRepository::class)]
#[Index(
    columns: ['id'],
    name: 'btree_lottery_award_idx',
    options: ['using' => 'btree']
)]
class LotteryAward
{
    use AggregateRoot;

    public function __construct(
        #[ORM\Embedded(class: LotteryAwardId::class, columnPrefix: false)]
        private LotteryAwardId $id,
        #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
        private string $lotteryId,
        #[ORM\Column(type: 'string', nullable: false, enumType: AwardStatus::class)]
        private AwardStatus $status,
        #[ORM\Column(type: 'decimal', precision: 10, scale: 2, nullable: false)]
        private string $winSum,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: false, updatable: false)]
        private DateTimeImmutable $createdAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $updatedAt,
        #[ORM\Column(type: 'datetimetz_immutable', nullable: true)]
        private ?DateTimeImmutable $deletedAt,
    ) {
        $this->init();
    }

    /**
     * @throws \Exception
     */
    public static function createAward(LotteryDeterminedWinner $event): self
    {
        $award = new self(
            id: new LotteryAwardId(Uuid::uuid7()),
            lotteryId: $event->lotteryId,
            status: AwardStatus::PLAYED_OUT,
            winSum: $event->winSum,
            createdAt: new DateTimeImmutable('now'),
            updatedAt: null,
            deletedAt: null
        );

        $award->recordEvent(domainEvent: new AwardCreated(
            aggregateId: $award->getId()->getIdString(),
            lotteryId: $award->getLotteryId(),
            winSum: $award->getWinSum()
        ));

        return $award;
    }

    public function getId(): LotteryAwardId
    {
        return $this->id;
    }

    public function getLotteryId(): string
    {
        return $this->lotteryId;
    }

    public function getStatus(): AwardStatus
    {
        return $this->status;
    }

    public function getWinSum(): string
    {
        return $this->winSum;
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
