<?php

declare(strict_types=1);

namespace App\Lottery\Application\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:process_consume',
    description: 'Run process consume message from Kafka topic',
    hidden: false
)]
final class ProcessConsumeCommand extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        parent::execute($input, $output);

        return Command::SUCCESS;
    }
}
