<?php

declare(strict_types=1);

namespace Lottery\Model;

use App\Lottery\Model\LotteryId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LotteryIdTest extends TestCase
{
    public function testGenerateUuidV7(): void
    {
        $lotteryId = LotteryId::generateUuidV7();

        $this->assertInstanceOf(LotteryId::class, $lotteryId);
        $this->assertInstanceOf(UuidInterface::class, $lotteryId->getId());
        $this->assertSame('7', substr($lotteryId->getId()->toString(), 14, 1));
    }

    public function testInvalidUuidVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid UUID version from LotteryId. Must be version 7.");

        $uuid = Uuid::uuid4();

        new LotteryId($uuid);
    }

    public function testValidUuid(): void
    {

        $uuid = Uuid::uuid7();

        $lotteryId = new LotteryId($uuid);

        $this->assertInstanceOf(LotteryId::class, $lotteryId);
    }
}
