<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use App\Core\Infrastructure\Kafka\Consumer\KafkaConsumerService;
use App\Lottery\Application\UseCase\LotteryCreateHandler;
use Psr\Log\LoggerInterface;
use RdKafka\Message;
use React\EventLoop\LoopInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use SplQueue;
use Throwable;

#[AsCommand(
    name: 'app:consume'
)]
final class KafkaWorker extends Command
{
    private const BATCH_SIZE = 10;

    private readonly SplQueue $messageQueue;

    public function __construct(
        private readonly KafkaConsumerService $consumer,
        private readonly LoopInterface $loop,
        private readonly LotteryCreateHandler $handler,
        private readonly LoggerInterface $logger,
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
        $this->loop->addPeriodicTimer(interval: 1, callback: function () use ($output) {
            $this->consumeAndProcessMessages($output);
        });

        $this->loop->run();

        return Command::SUCCESS;
    }

    /**
     * @throws Throwable
     * @psalm-suppress UndefinedConstant
     */
    private function consumeAndProcessMessages(OutputInterface $output): void
    {
        $message = $this->consumer->consumeFromKafka();

        switch ($message->err) {
            case RD_KAFKA_RESP_ERR_NO_ERROR:
                $this->enqueueMessage($message);
                break;
            case RD_KAFKA_RESP_ERR__PARTITION_EOF:
                $this->logMessage("No more messages; will wait for more", $output);
                break;
            case RD_KAFKA_RESP_ERR__TIMED_OUT:
                $this->logMessage("Timed out", $output);
                break;
            default:
                $this->logger->error(
                    message: $message->errstr(),
                    context: [
                        'kafkaError' => $message->err,
                    ]
                );
        }
    }

    private function enqueueMessage(Message $message): void
    {

        $increment = $this->incrementCounter();

        $this->messageQueue->enqueue(
            new KafkaQueuedMessage($message, $increment)
        );

        if ($this->messageQueue->count() >= self::BATCH_SIZE) {
            $this->processBatchSize();
        }
    }

    private function incrementCounter(): int
    {
        static $increment = 0;
        ++$increment;

        return $increment;
    }

    private function processBatchSize(): void
    {
        while (!$this->messageQueue->isEmpty()) {
            $queuedMessage = $this->messageQueue->dequeue();
            $message = $queuedMessage->getMessage();

            $this->handler->handle(message: $message->payload);
        }
    }

    private function logMessage(string $message, OutputInterface $output): void
    {
        $greenStyle = new OutputFormatterStyle('green');

        $output->getFormatter()->setStyle('green', $greenStyle);

        $output->writeln('<green>' . $message . '</green>');
    }
}
