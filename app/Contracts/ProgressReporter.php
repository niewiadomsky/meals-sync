<?php

namespace App\Contracts;

interface ProgressReporter
{
    /**
     * Start a new progress task
     */
    public function start(string $task, int $totalSteps): void;

    /**
     * Advance the progress by one step
     */
    public function advance(int $steps = 1): void;

    /**
     * Finish the current progress task
     */
    public function finish(): void;

    /**
     * Set the current progress message
     */
    public function setMessage(string $message): void;

    public function log(string $message): void;
} 