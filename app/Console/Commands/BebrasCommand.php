<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Bebras\Migrator;
use DB;

class BebrasCommand extends Command
{
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
        $arguments = collect($this->arguments())->except('command');
        $connection = $this->connectDB($this->arguments());
        $migrator = new Migrator($this, $connection);
        $migrator->run();
    }


    private function connectDB($arguments) {
        $arguments = collect($arguments)
            ->except('command')
            ->merge([
                'driver' => 'mysql',
                'port' => '3306',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
            ->toArray();
        config()->set('database.connections.bebras', $arguments);
        return DB::connection('bebras');
    }
}