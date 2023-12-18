<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use InvalidArgumentException;

#[Embeddable]
class LotteryId
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
        private UuidInterface $id
    ) {
        if (!Uuid::isValid($this->id->toString())) {
            throw new InvalidArgumentException('Invalid UUID.');
        }
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public static function generateUuidV7(): self
    {
        return new self(Uuid::uuid7());
    }

    public function equals(self $other): bool
    {
        //ToDo to refactor
        return Uuid::fromString($this->getId()->toString()) == $other;
    }
}
