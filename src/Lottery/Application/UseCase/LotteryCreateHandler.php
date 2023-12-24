<?php

declare(strict_types=1);

namespace App\Lottery\Application\UseCase;

use App\Lottery\Application\Command\CreateLotteryCommand;
use App\Lottery\Model\Lottery;
use App\Lottery\Model\WriteLotteryStorage;
use Psr\Log\LoggerInterface;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use Throwable;

final readonly class LotteryCreateHandler
{
    public function __construct(
        private WriteLotteryStorage $writeLotteryStorage,
        private LoggerInterface $logger,
        private LoopInterface $loop
    ) {
    }

    /**
     * @psalm-suppress MixedArgumentTypeCoercion
     */
    public function handleAsync(string $message, Deferred $deferred): PromiseInterface
    {
        try {
            $this->parseMessageAsync(deferred: $deferred, message: $message)
                ->then(
                    function (array $messageData) use ($deferred): PromiseInterface {
                        return $this->createLotteryAsync(deferred: $deferred, messageData: $messageData);
                    }
                );
        } catch (Throwable $exception) {
            $this->logger->error(
                $exception->getMessage(),
                [
                    'trace' => $exception->getTrace(),
                ]
            );
            $deferred->reject($exception);
        }

        return $deferred->promise();
    }

    private function parseMessageAsync(Deferred $deferred, string $message): PromiseInterface
    {
        $this->loop->addTimer(0, function () use ($deferred, $message) {
            try {
                $deferred->resolve(json_decode($message, true, JSON_THROW_ON_ERROR));
            } catch (Throwable $exception) {
                $deferred->reject($exception);
            }
        });

        return $deferred->promise();
    }

    /**
     * @param array{
     *     message: array{
     *      eventType: string
     *    },
     *     game: array{
     *      playerId: string,
     *      gameId: string,
     *      stake: string
     * }} $messageData
     */
    private function createLotteryAsync(Deferred $deferred, array $messageData): PromiseInterface
    {
        $this->loop->addTimer(0, function () use ($deferred, $messageData) {
            try {
                $lottery = Lottery::createStartLottery(
                    new CreateLotteryCommand(
                        playerId: $messageData['game']['playerId'],
                        gameId: $messageData['game']['gameId'],
                        stake: $messageData['game']['stake']
                    )
                );
                $this->writeLotteryStorage->createLottery($lottery);
                $deferred->resolve($lottery);
            } catch (Throwable $exception) {
                $deferred->reject($exception);
            }
        });

        return $deferred->promise();
    }
}
