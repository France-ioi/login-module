<?php

namespace App\LoginModule\Bebras;

use DB;
use App\LoginModule\Bebras\Mappers\UserMapper;

class Data {

    public static function queryUsers($connection, $offset, $amount) {
        return $connection->table('user')
            ->select('*')
            ->where('isAdmin', 0)
            ->skip($offset)
            ->take($amount)
            ->get()
            ->map(function($row) {
                return UserMapper::remap($row);
            });

    }

}