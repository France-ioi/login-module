<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\DataSync\Migrator;

class DataSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lm:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync data: login module v1 -> login module v2';

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
        $migrator = new Migrator();
        $migrator->migrate();
    }
}
