<?php

namespace App\LoginModule\Migrators;

use DB;

trait ExternalDatabaseCommand {

    private function connectExternalDB() {
        $arguments = collect($this->arguments())
            ->except('command')
            ->merge([
                'driver' => 'mysql',
                'port' => '3306',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
            ->toArray();
        config()->set('database.connections.external_source', $arguments);
        return DB::connection('external_source');
    }

}