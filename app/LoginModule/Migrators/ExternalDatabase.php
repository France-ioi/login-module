<?php

namespace App\LoginModule\Migrators;

use DB;

class ExternalDatabase
{

    const CONNECTION_NAME = 'external_source';

    public static function connect($params) {
        $arguments = collect($params)
            ->except('command')
            ->merge([
                'driver' => 'mysql',
                'port' => '3306',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci'
            ])
            ->toArray();
        config()->set('database.connections.'.self::CONNECTION_NAME, $arguments);
        DB::connection(self::CONNECTION_NAME)->setQueryGrammar(new QueryGrammar());
        return DB::connection(self::CONNECTION_NAME);
    }


    public static function disconnect() {
        DB::disconnect(self::CONNECTION_NAME);
    }

}