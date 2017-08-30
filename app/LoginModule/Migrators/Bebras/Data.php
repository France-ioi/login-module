<?php

namespace App\LoginModule\Migrators\Bebras;

use DB;
use App\LoginModule\Migrators\Bebras\Mappers\UserMapper;

class Data
{

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


    public static function updateExternalUser($connection, $id, $data) {
        return $connection->table('user')
            ->where('ID', $id)
            ->update($data);
    }

}