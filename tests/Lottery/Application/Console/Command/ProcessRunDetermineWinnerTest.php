<?php

declare(strict_types=1);

namespace Lottery\Application\Console\Command;

use App\Lottery\Application\Console\Command\ProcessRunDetermineWinner;
use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\UseCase\LotteryFinishedStatusUpdateHandler;
use App\Lottery\Model\LotteryId;
use App\Lottery\Model\ReadLotteryStorage;
use App\Lottery\Model\WriteLotteryStorage;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessRunDetermineWinnerTest extends TestCase
{
    public function testExecuteWithLotteryList(): void
    {
        $this->markTestSkipped();
        $lotteryListStarted = [
            new LotteryListInStarted(
                lotteryId: "018c8334-f298-7175-a9a7-035f540e567a",
                gameId: "018c8335-51e7-70f0-ad2f-11dea315200a",
                playerId: "018c8335-80ff-705f-a113-cc14072ed053",
                stake: '1000'
            ),
        ];

        $lotteryId = new LotteryId(Uuid::fromString("018c8334-f298-7175-a9a7-035f540e567a"));

        $readLotteryStorageMock = $this->createMock(originalClassName: ReadLotteryStorage::class);
        $writeLotteryStoreMock = $this->createMock(originalClassName: WriteLotteryStorage::class);

        $readLotteryStorageMock
            ->expects($this->exactly(count: 2))
            ->method(constraint: 'getLotteryListInStarted')
            ->willReturn(
                value: $lotteryListStarted
            );

        $writeLotteryStoreMock
            ->expects($this->exactly(count: 2))
            ->method(constraint: 'updateLotteryStatusToFinished')
            ->with(arguments: $this->equalTo(
                $lotteryId
            ))
        ;

        $finishedStatusUpdateHandlerMock = new LotteryFinishedStatusUpdateHandler(
            writeLotteryStorage: $writeLotteryStoreMock
        );
        $eventDispatcherMock = $this->createMock(originalClassName: EventDispatcherInterface::class);
        $loggerMock = $this->createMock(originalClassName: LoggerInterface::class);

        $command = new ProcessRunDetermineWinner(
            readLotteryStorage: $readLotteryStorageMock,
            finishedStatusUpdateHandler: $finishedStatusUpdateHandlerMock,
            eventDispatcher: $eventDispatcherMock,
            logger: $loggerMock
        );

        $commandTester = new CommandTester($command);



        $input = new ArrayInput([]);
        $output = new BufferedOutput();


        $commandTester->execute([]);
        $statusCode = $command->run($input, $output);
        $this->assertEquals(Command::SUCCESS, $statusCode);
    }
}
