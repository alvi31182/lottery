<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use App\Core\Infrastructure\Kafka\Consumer\KafkaConsumerService;
use App\Lottery\Application\UseCase\LotteryCreateHandler;
use Psr\Log\LoggerInterface;
use RdKafka\Message;
use React\EventLoop\LoopInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
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
        $this->loop->addSignal(SIGINT, function (int $signal) use ($output): void {
            $output->writeln('Worker stop from user from signal ' . $signal);
            $this->stopWorker();
        });

        $this->loop->addPeriodicTimer(interval: 0.00001, callback: function () use ($output) {
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
        $this->messageQueue->enqueue($message);

        $this->processHandleMessage();
    }

    private function processHandleMessage(): void
    {
        while (!$this->messageQueue->isEmpty()) {
            $deferred = new Deferred();

            $message = $this->messageQueue->dequeue();

            $this->handleMessageAsync(payload: $message->payload, deferred: $deferred)
                ->then(
                    function (): void {
                        $this->processHandleMessage();
                    },
                    function (Throwable $exception): void {
                        $this->logger->error(
                            message: $exception->getMessage()
                        );
                    }
                );
        }
    }

    private function handleMessageAsync(string $payload, Deferred $deferred): PromiseInterface
    {
        return $this->handler->handleAsync(message: $payload, deferred: $deferred);
    }

    private function stopWorker(): void
    {
        $this->loop->stop();
    }

    private function logMessage(string $message, OutputInterface $output): void
    {
        $greenStyle = new OutputFormatterStyle('green');

        $output->getFormatter()->setStyle('green', $greenStyle);

        $output->writeln('<green>' . $message . '</green>');
    }
}
