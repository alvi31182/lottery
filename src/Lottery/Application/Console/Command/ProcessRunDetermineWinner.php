<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\Command\Award\CreateLotteryAwardCommand;
use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\UseCase\CreateLotteryAward;
use App\Lottery\Model\ReadLotteryStorage;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:determine_winner',
    description: 'Run determine winner by status started',
    hidden: false
)]
final class ProcessRunDetermineWinner extends Command
{
    private const PERCENT = 33;

    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly CreateLotteryAward $createLotteryAward,
        string $name = null
    ) {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $lotteryListForStarted = $this->readLotteryStorage->getLotteryListInStarted();

        [$prize, $winner] = $this->runSelectionWinner(lotteryListForStarted: $lotteryListForStarted);

        $this->createLotteryAward->handle(
            new CreateLotteryAwardCommand(
                winSum: (string) $prize,
                lotteryId: $winner->lotteryId
            )
        );

        dd($winner);
    }

    /**
     * @param array<LotteryListInStarted> $lotteryListForStarted
     *
     * @return array{0:float, 1:LotteryListInStarted}
     */
    private function runSelectionWinner(array $lotteryListForStarted): array
    {
        $winnerIndex = array_rand($lotteryListForStarted);
        $winner = $lotteryListForStarted[$winnerIndex];

        $stake = (float)$winner->stake;
        $prize = $stake * (self::PERCENT / 100);

        return [$prize, $winner];
    }
}
