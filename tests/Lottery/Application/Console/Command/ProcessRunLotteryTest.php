<?php

declare(strict_types=1);

namespace Lottery\Application\Console\Command;

use App\Lottery\Application\Console\Command\ProcessRunLottery;
use App\Lottery\Application\Dto\LotteryListInWaiting;
use App\Lottery\Application\UseCase\UpdateLotteryStatus;
use App\Lottery\Model\ReadLotteryStorage;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ProcessRunLotteryTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testRunSelectionWinner(): void
    {
        $lotteryList = [
            new LotteryListInWaiting('player1', 'game1'),
            new LotteryListInWaiting('player2', 'game2'),
        ];

        $readLotteryStorage = $this->createMock(ReadLotteryStorage::class);
        $readLotteryStorage->expects($this->once())
            ->method('getLotteryListByStatusInWaiting')
            ->willReturn($lotteryList);

        $updateLotteryStatus = $this->createMock(UpdateLotteryStatus::class);

        $application = new Application();
        $application->add(new ProcessRunLottery($readLotteryStorage, $updateLotteryStatus));

        $command = $application->find('app:lottery_list');
        $commandTester = new CommandTester($command);

        $commandTester->execute([]);

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Winner selected', $output);
    }
}
