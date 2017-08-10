<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\Merge\Migrator;
use DB;

class LoginModuleMergeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lm:merge
        {host : login-module mysql host }
        {port : login-module mysql port }
        {database : login-module mysql database }
        {username : login-module mysql username }
        {password : login-module mysql password }
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merge two login-module databases';

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
        $arguments = collect($this->arguments())->except('command');
        $connection = $this->connectDB($this->arguments());
        $migrator = new Migrator($this, $connection);
        $migrator->run();
    }
}
