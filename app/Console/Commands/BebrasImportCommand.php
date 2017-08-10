<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\Bebras\Migrator;
use App\LoginModule\Migrators\ExternalDatabaseCommand;


class BebrasImportCommand extends Command
{

    use ExternalDatabaseCommand;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bebras:import
        {host : bebras mysql host }
        {port : bebras mysql port }
        {database : bebras mysql database }
        {username : bebras mysql username }
        {password : bebras mysql password }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import accounts from bebras instance';

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
        $migrator = new Migrator($this, $this->connectExternalDB());
        $migrator->run();
    }

}