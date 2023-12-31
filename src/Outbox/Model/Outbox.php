<?php

declare(strict_types=1);

namespace App\Outbox\Model;

use App\Lottery\Model\Events\AwardCreated;
use App\Outbox\Infrastructure\Persistence\Doctrine\OutboxRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[ORM\Entity(
    repositoryClass: OutboxRepository::class
)]
#[ORM\Table(
    name: 'Outbox'
)]
class Outbox
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
        private UuidInterface $id,
        #[ORM\Column(type: 'string', unique: false, nullable: false)]
        private string $eventName,
        #[ORM\Column(type: 'jsonb', nullable: false)]
        private array $eventData,
        #[ORM\Column(type: 'boolean', nullable: false, options: ['default' => false])]
        private bool $isSend,
        #[ORM\Column(type: 'timestamp', nullable: false)]
        private DateTimeImmutable $createdAt
    ) {
    }

    /**
     * @param  array{
     *     message_event: string,
     *     lottery: array{
     *         lottery_id: string,
     *         win_sum: string,
     *         created_at: DateTimeImmutable
     *     }
     * } $eventData
     */
    public static function create(AwardCreated $event, array $eventData): self
    {
        return new self(
            id: Uuid::uuid7(),
            eventName: $event->eventName(),
            eventData: $eventData,
            isSend: false,
            createdAt: new DateTimeImmutable('now')
        );
    }

    public function statusIsSendToTrue(): self
    {
        $this->isSend = true;

        return $this;
    }
}
