<?php

declare(strict_types=1);

namespace Lottery\Application\Console\Command;

use App\Lottery\Application\Console\Command\ProcessStartLottery;
use App\Lottery\Application\Event\EventData\LotteryStatusUpdated;
use App\Lottery\Model\ReadLotteryStorage;
use PHPUnit\Framework\MockObject\Exception;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProcessRunLotteryTest extends KernelTestCase
{
    /**
     * @throws Exception
     * @throws ExceptionInterface
     */
    public function testRunSelectionWinner(): void
    {
        $readLotteryStorageMock = $this->createMock(ReadLotteryStorage::class);
        $eventDispatcherMock = $this->createMock(EventDispatcherInterface::class);

        $lotteryList = ['lottery1', 'lottery2'];
        $readLotteryStorageMock
            ->expects($this->once())
            ->method('getLotteryListByStatusInWaiting')
            ->willReturn($lotteryList);

        $eventDispatcherMock
            ->expects($this->once())
            ->method('dispatch')
            ->with(
                $this->isInstanceOf(LotteryStatusUpdated::class),
                LotteryStatusUpdated::NAME
            );

        $command = new ProcessStartLottery($readLotteryStorageMock, $eventDispatcherMock);

        $input = new ArrayInput([]);
        $output = new BufferedOutput();


        $statusCode = $command->run($input, $output);

        $this->assertEquals(Command::SUCCESS, $statusCode);
    }
}
