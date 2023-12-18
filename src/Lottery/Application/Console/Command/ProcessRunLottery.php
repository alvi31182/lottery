<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\Event\LotteryStatusUpdated;
use App\Lottery\Model\ReadLotteryStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

#[AsCommand(
    name: 'app:lottery_list',
    description: 'Run process consume message from Kafka topic',
    hidden: false
)]
final class ProcessRunLottery extends Command
{
    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly EventDispatcherInterface $eventDispatcher,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lotteryList = $this->readLotteryStorage->getLotteryListByStatusInWaiting();

        $this->eventDispatcher->dispatch(
            event: new LotteryStatusUpdated(
                lotteryList: $lotteryList
            ),
            eventName: LotteryStatusUpdated::NAME
        );

        return Command::SUCCESS;
    }
}
