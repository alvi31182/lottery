<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use Doctrine\ORM\Mapping\Embeddable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[Embeddable]
class LotteryId
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique:true, nullable: false)]
        #[ORM\GeneratedValue(strategy:'CUSTOM')]
        #[ORM\CustomIdGenerator(class:UuidGenerator::class)]
        private string $id
    ) {
        $uuid = Uuid::fromString($id);

        if (!Uuid::isValid($this->id)) {
            throw new InvalidArgumentException('Invalid UUIDv7.');
        }
    }

    public function getId(): string
    {
        return $this->id;
    }

    public static function generateUuidV7(): UuidInterface
    {
        return Uuid::uuid7();
    }

    public function equals(self $other): bool
    {
        return Uuid::fromString($this->getId())->equals(Uuid::fromString($other->getId()));
    }
}
