<?php

declare(strict_types=1);

namespace App\Core\Worker\Kafka;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class KafkaWorker extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        return Command::SUCCESS;
    }
}
