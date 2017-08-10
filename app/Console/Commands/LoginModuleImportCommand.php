<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\Import\Migrator;

class LoginModuleImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lm:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import accounts data from old (v1) database';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $migrator = new Migrator($this);
        $migrator->run();
    }
}
