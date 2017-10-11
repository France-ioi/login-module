<?php

namespace App\LoginModule\Migrators\Import;

use App\LoginModule\Migrators\Import\Mappers\UserMapper;
use App\LoginModule\Migrators\Import\Mappers\BadgeMapper;

class Data
{

    public static function queryUsers($connection, $offset, $amount) {
        return $connection->table('users')
            ->select('*')
            ->skip($offset)
            ->take($amount)
            ->get()
            ->map(function($row) {
                return UserMapper::remap($row);
            });
    }


    public static function queryBadges($connection, $user_id) {
        return $connection->table('user_badges')
            ->select('*')
            ->where('idUser', $user_id)
            ->get()
            ->map(function($row) {
                return BadgeMapper::remap($row);
            });
    }


    public static function queryAuths($connection, $user_id) {
        return $connection->table('users_auths')
            ->select('*')
            ->where('idUser', $user_id)
            ->get();
    }

}