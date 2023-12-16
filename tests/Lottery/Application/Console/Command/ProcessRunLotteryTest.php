<?php

declare(strict_types=1);

namespace Lottery\Application\Console\Command;

use App\Lottery\Application\Console\Command\ProcessRunLottery;
use App\Lottery\Application\Dto\LotteryList;
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
            new LotteryList('player1', 'game1', '100.00'),
            new LotteryList('player2', 'game2', '150.00'),
        ];

        $readLotteryStorage = $this->createMock(ReadLotteryStorage::class);
        $readLotteryStorage->expects($this->once())
            ->method('getLotteryList')
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
