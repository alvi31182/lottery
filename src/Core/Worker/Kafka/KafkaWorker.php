<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use App\Core\Infrastructure\Kafka\Consumer\KafkaConsumerService;
use App\Lottery\Application\UseCase\LotteryCreateHandler;
use React\EventLoop\LoopInterface;
use SplQueue;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

#[AsCommand(
    name: 'app:consume'
)]
final class KafkaWorker extends Command
{
    private const BATCH_SIZE = 1;
    private const MESSAGE_COUNT = 0;
    private readonly SplQueue $messageQueue;

    public function __construct(
        private readonly KafkaConsumerService $consumer,
        private readonly LoopInterface $loop,
        private readonly LotteryCreateHandler $handler,
        string $name = null
    ) {
        $this->messageQueue = new SplQueue();
        parent::__construct($name);
    }

    /**
     * @psalm-suppress UndefinedConstant
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $increment = self::MESSAGE_COUNT;
        $this->loop->addPeriodicTimer(interval: 1, callback: function () use (&$increment) {
            $message = $this->consumer->consumeFromKafka();

            /* @psalm-suppress UndefinedConstant */
            switch ($message->err) {
                case RD_KAFKA_RESP_ERR_NO_ERROR:
                    $this->messageQueue->enqueue(
                        new KafkaQueuedMessage(
                            message: $message,
                            serialNumber: $increment
                        )
                    );

                    if ($this->messageQueue->count() >= self::BATCH_SIZE) {
                        $this->processBatchSize();
                        $increment = 0;
                    }
                    ++$increment;

                    break;
                case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                    echo "No more messages; will wait for more\n";
                    break;
                case RD_KAFKA_RESP_ERR__TIMED_OUT:
                    echo "Timed out\n";
                    break;
                default:
                    throw new Exception($message->errstr(), $message->err);
            }
        });

        $this->loop->run();

        return Command::SUCCESS;
    }

    private function processBatchSize(): void
    {
        while (!$this->messageQueue->isEmpty()) {
            $queuedMessage = $this->messageQueue->dequeue();
            $message = $queuedMessage->getMessage();

            $this->handler->handler(message: $message->payload);
        }
    }
}
