<?php

declare(strict_types=1);

namespace App\Lottery\Model;

use Doctrine\ORM\Mapping\Embeddable;
use Ramsey\Uuid\UuidInterface;
use Doctrine\ORM\Mapping as ORM;

#[Embeddable]
class LotteryId
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: "uuid", unique:true, nullable: false)]
        #[ORM\GeneratedValue(strategy:"CUSTOM")]
        #[ORM\CustomIdGenerator(class:"Ramsey\Uuid\Doctrine\UuidGenerator")]
        private UuidInterface $id
    ) {
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function equals(self $other): bool
    {
        return $this->id->equals($other->getId());
    }
}
