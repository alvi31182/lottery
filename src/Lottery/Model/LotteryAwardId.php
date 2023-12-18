<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[Embeddable]
class LotteryAwardId
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
        private UuidInterface $id
    ) {
    }

    public function getIdString(): string
    {
        return $this->id->toString();
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public static function generateUuidV7(): UuidInterface
    {
        return Uuid::uuid7();
    }

    public function equals(self $other): bool
    {
        //ToDo need refactor
        return $this->id->equals($other->getId());
    }
}
