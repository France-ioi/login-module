<?php

namespace App\LoginModule\Migrators;

use Illuminate\Database\Query\Grammars\MySqlGrammar;

class MySqlInsertIgnoreGrammar extends MySqlGrammar
{

    public function compileInsert(\Illuminate\Database\Query\Builder $query, array $values) {
        return preg_replace(
            '/^insert into/',
            'insert ignore into',
            parent::compileInsert($query, $values),
            1
        );
    }

}