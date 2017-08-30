<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\MigratorCommand;

class BebrasImportCommand extends Command
{

    use MigratorCommand;

    protected $signature = 'bebras:import
        {host : bebras mysql host }
        {port : bebras mysql port }
        {database : bebras mysql database }
        {username : bebras mysql username }
        {password : bebras mysql password }
    ';

    protected $description = 'Import accounts from bebras instance';

    protected $migrator_class = \App\LoginModule\Migrators\Bebras\Migrator::class;

}