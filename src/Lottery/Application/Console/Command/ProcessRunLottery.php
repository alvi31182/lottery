<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\UseCase\UpdateLotteryStatus;
use App\Lottery\Model\ReadLotteryStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:lottery_list',
    description: 'Run process consume message from Kafka topic',
    hidden: false
)]
final class ProcessRunLottery extends Command
{
    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly UpdateLotteryStatus $updateLotteryStatus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lotteryList = $this->readLotteryStorage->getLotteryListByStatusInWaiting();

        if (!empty($lotteryList)) {
            $this->updateLotteryStatus->updateStatus(lotteryList: $lotteryList);
        }

        return Command::SUCCESS;
    }
}
