<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Embeddable;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use InvalidArgumentException;

/**
 * @psalm-immutable
 */
#[Embeddable]
class LotteryId
{
    /* @var non-empty-string */
    private const UUID_VERSION = '7';

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'uuid', unique: true, nullable: false)]
        #[ORM\GeneratedValue(strategy: 'CUSTOM')]
        #[ORM\CustomIdGenerator(class: UuidGenerator::class)]
        private UuidInterface $id
    ) {
        $this->validateUuid(uuid: $this->id->toString());
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public static function generateUuidV7(): self
    {
        return new self(Uuid::uuid7());
    }

    private function validateUuid(string $uuid): void
    {
        if (!Uuid::isValid($uuid)) {
            throw new InvalidArgumentException("Invalid UUID format from LotteryId." . $uuid);
        }

        if (substr($uuid, 14, 1) !== self::UUID_VERSION) {
            throw new InvalidArgumentException("Invalid UUID version from LotteryId. Must be version 7.");
        }
    }
}
