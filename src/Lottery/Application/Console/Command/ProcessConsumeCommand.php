<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\Dto\LotteryList;
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
final class ProcessConsumeCommand extends Command
{
    private const PERCENT = 33;
    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly UpdateLotteryStatus $updateLotteryStatus,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lotteryList = $this->readLotteryStorage->getLotteryList();

        [$prize, $winner] = $this->runSelectionWinner(lotteryList: $lotteryList);

        $output->write(
            messages: sprintf(
                'Winner selected, this playerId %s winner prize %s',
                $winner->playerId,
                $prize . PHP_EOL
            )
        );

        return Command::SUCCESS;
    }

    /**
     * @param array<LotteryList> $lotteryList
     *
     * @return array{0:float, 1:LotteryList}
     */
    private function runSelectionWinner(array $lotteryList): array
    {
        $winnerIndex = array_rand($lotteryList);
        $winner = $lotteryList[$winnerIndex];

        $stake = (float) $winner->stake;
        $prize = $stake * (self::PERCENT / 100);

        return [$prize, $winner];
    }
}
