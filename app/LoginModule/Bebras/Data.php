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


    public static function updateUserExternalId($connection, $bebras_id, $login_module_id) {
        return $connection->table('user')
            ->where('ID', $bebras_id)
            ->update(['externalID' => $login_module_id]);
    }

}