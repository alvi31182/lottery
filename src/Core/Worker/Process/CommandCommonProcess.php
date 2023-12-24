<?php

declare(strict_types=1);

namespace App\Core\Worker\Process;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'app:run_commands',
    description: 'This this is a common command that run all commands',
    hidden: false
)]
class CommandCommonProcess extends Command
{
    public function __construct(string $name = null)
    {
        parent::__construct($name);
    }
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $process = new Process(['ls', 'app:start_lottery']);
        $process->start();

        while ($process->isRunning()) {
            echo '1+';
        }

        echo $process->getOutput();
        return Command::SUCCESS;
    }
}
