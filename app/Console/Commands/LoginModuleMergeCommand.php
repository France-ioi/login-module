<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\LoginModule\Migrators\MigratorCommand;

class LoginModuleMergeCommand extends Command
{

    use MigratorCommand;

    protected $signature = 'lm:merge
        {id : source login-module instance id }
        {host : source login-module mysql host }
        {port : source login-module mysql port }
        {database : source login-module mysql database }
        {username : source login-module mysql username }
        {password : source login-module mysql password }
    ';

    protected $description = 'Merge two login-module databases';

    protected $migrator_class  = \App\LoginModule\Migrators\Merge\Migrator::class;

}
