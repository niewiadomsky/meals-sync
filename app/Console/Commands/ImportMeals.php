<?php

namespace App\Console\Commands;

use App\Services\ConsoleProgressReporter;
use App\Services\MealImporter;
use Illuminate\Console\Command;

class ImportMeals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:meals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(MealImporter $importer)
    {

        $progressReporter = new ConsoleProgressReporter($this);
        $importer->setProgressReporter($progressReporter);
        $importer->import();

        $this->info('Meals imported successfully');
    }
}
