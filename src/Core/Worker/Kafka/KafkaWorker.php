<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use App\Core\Infrastructure\Kafka\Consumer\KafkaConsumerService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:consume'
)]
final class KafkaWorker extends Command
{
    public function __construct(
        private readonly KafkaConsumerService $consumer,
        //  private readonly LoopInterface $loop,
        string $name = null
    ) {
        parent::__construct($name);
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        while (true) {
            $consumer = $this->consumer->consumeFromKafka();

            sleep(1);
        }





        return Command::SUCCESS;
    }
}
