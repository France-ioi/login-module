<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\MigratorCommand;

class LoginModuleImportCommand extends Command
{

    use MigratorCommand;

    protected $signature = 'lm:import
        {host : old login-module mysql host }
        {port : old login-module mysql port }
        {database : old login-module mysql database }
        {username : old login-module mysql username }
        {password : old login-module mysql password }
    ';

    protected $description = 'Import accounts data from old (v1) database';

    protected $migrator_class  = \App\LoginModule\Migrators\Import\Migrator::class;

}
