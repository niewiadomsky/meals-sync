<?php

namespace App\Services;

use App\Contracts\ProgressReporter;
use Illuminate\Console\Command;
use Symfony\Component\Console\Helper\ProgressBar;

class ConsoleProgressReporter implements ProgressReporter
{
    private ?ProgressBar $progressBar = null;

    public function __construct(
        private Command $command
    ) {}

    public function start(string $task, int $totalSteps): void
    {
        $this->command->info("Starting: {$task}");
        $this->progressBar = $this->command->getOutput()->createProgressBar($totalSteps);
        $this->progressBar->start();
    }

    public function advance(int $steps = 1): void
    {
        $this->progressBar?->advance($steps);
    }

    public function finish(): void
    {
        $this->progressBar?->finish();
        $this->command->newLine();
        $this->progressBar = null;
    }

    public function setMessage(string $message): void
    {
        $this->progressBar?->setMessage($message);
    }

    public function log(string $message): void
    {
        $this->command->info($message);
    }
} 