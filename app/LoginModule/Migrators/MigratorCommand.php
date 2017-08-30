<?php

namespace App\LoginModule\Migrators;

trait MigratorCommand
{

    public function handle() {
        $params = collect($this->arguments())->except('command');
        $connection = ExternalDatabase::connect($params);
        $migrator = new $this->migrator_class($this, $connection);
        $migrator->run();
        ExternalDatabase::disconnect();
    }

}