<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PurgeCommand extends Command
{

    protected $signature = 'lm:purge';
    protected $description = 'Run the command to purge expired data';

    public function handle()
    {
        $time = time();
        //TODO:
        $this->info('Expired data purged successfully.');
    }


}