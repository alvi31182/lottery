<?php

declare(strict_types=1);

namespace Lottery\Model;

use App\Lottery\Model\LotteryAwardId;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class LotteryAwardIdTest extends TestCase
{
    public function testGenerateUuidV7(): void
    {
        $lotteryAwardId = LotteryAwardId::generateUuidV7();

        $this->assertInstanceOf(LotteryAwardId::class, $lotteryAwardId);
        $this->assertInstanceOf(UuidInterface::class, $lotteryAwardId->getId());
        $this->assertSame('7', substr($lotteryAwardId->getId()->toString(), 14, 1));
    }

    public function testInvalidUuidVersion(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage("Invalid UUID version from LotteryAwardId. Must be version 7.");

        $uuid = Uuid::uuid4();

        new LotteryAwardId($uuid);
    }

    public function testValidUuid(): void
    {

        $uuid = Uuid::uuid7();

        $lotteryAwardId = new LotteryAwardId($uuid);

        $this->assertInstanceOf(LotteryAwardId::class, $lotteryAwardId);
    }
}
