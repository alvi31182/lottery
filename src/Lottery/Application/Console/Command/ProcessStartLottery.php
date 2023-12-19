<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\Command\UpdateLotteryToStartCommand;
use App\Lottery\Application\UseCase\LotteryUpdateStatusToStartedHandler;
use App\Lottery\Model\ReadLotteryStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:get_lottery_list',
    description: 'Run process consume message from Kafka topic, after as consumed message and stored in DB.',
    hidden: false
)]
final class ProcessStartLottery extends Command
{
    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly LotteryUpdateStatusToStartedHandler $handler,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lotteryList = $this->readLotteryStorage->getLotteryListByStatusInWaiting();

        $this->handler->handle(
            command: new UpdateLotteryToStartCommand(
                lotteryList: $lotteryList
            )
        );

        return Command::SUCCESS;
    }
}
