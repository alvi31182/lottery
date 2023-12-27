<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use App\Lottery\Application\Command\UpdateToFinishedCommand;
use App\Lottery\Application\Dto\LotteryListInStarted;
use App\Lottery\Application\Events\EventData\LotteryDeterminedWinner;
use App\Lottery\Application\Exception\LotteryUpdateException;
use App\Lottery\Application\UseCase\LotteryFinishedStatusUpdateHandler;
use App\Lottery\Model\ReadLotteryStorage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Throwable;

#[AsCommand(
    name: 'app:determine_winner',
    description: 'Run determine winner by status started after as lottery status changes to started',
    hidden: false
)]
final class ProcessRunDetermineWinner extends Command
{
    private const PERCENT = 33;

    public function __construct(
        private readonly ReadLotteryStorage $readLotteryStorage,
        private readonly LotteryFinishedStatusUpdateHandler $finishedStatusUpdateHandler,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly LoggerInterface $logger,
        string $name = null
    ) {
        parent::__construct($name);
    }

    /**
     * @throws LotteryUpdateException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $lotteryListForStarted = $this->readLotteryStorage->getLotteryListInStarted();

            if (!empty($lotteryListForStarted)) {
                $this->handleLotterySelection(
                    lotteryListForStarted: $lotteryListForStarted,
                );
            } else {
                $this->handleEmptyLotteryList(output: $output);
            }
        } catch (Throwable $exception) {
            $this->logger->error(
                message: $exception->getMessage()
            );

            throw new LotteryUpdateException(
                $exception->getMessage()
            );
        }

        return Command::SUCCESS;
    }

    /**
     * @param array<LotteryListInStarted> $lotteryListForStarted
     *
     * @throws LotteryUpdateException
     */
    private function handleLotterySelection(array $lotteryListForStarted): void
    {
        [$prize, $winner] = $this->runSelectionWinner(lotteryListForStarted: $lotteryListForStarted);

        $this->updateToFinished(
            lotteryListInStarted: $winner
        );

        $this->dispatchLotteryDeterminedWinnerEvent(
            prize: (string)$prize,
            lotteryListInStarted: $winner
        );
    }

    /**
     * @throws LotteryUpdateException
     */
    private function updateToFinished(LotteryListInStarted $lotteryListInStarted): void
    {

        $this->finishedStatusUpdateHandler->handle(
            command: new UpdateToFinishedCommand(
                lotteryId: $lotteryListInStarted->lotteryId
            )
        );
    }

    /**
     * @see LotteryAwardCreateHandler::handle()
     */
    private function dispatchLotteryDeterminedWinnerEvent(
        string $prize,
        LotteryListInStarted $lotteryListInStarted
    ): void {
        $this->eventDispatcher->dispatch(
            event: new LotteryDeterminedWinner(
                winSum: $prize,
                lotteryId: $lotteryListInStarted->lotteryId
            ),
            eventName: LotteryDeterminedWinner::NAME
        );
    }

    private function handleEmptyLotteryList(OutputInterface $output): void
    {
        $greenStyle = new OutputFormatterStyle('green');

        $output->getFormatter()->setStyle('green', $greenStyle);

        $output->writeln('<green>' . 'Lottery list empty' . '</green>');
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
