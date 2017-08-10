<?php

namespace App\LoginModule\Migrators\Import;

use DB;
use App\LoginModule\Migrators\Import\Mappers\UserMapper;
use App\LoginModule\Migrators\Import\Mappers\BadgeMapper;

class Data {

    const CONNECTION = 'old';

    public static function queryUsers($offset, $amount) {
        return DB::connection(self::CONNECTION)
            ->table('users')
            ->select('*')
            ->skip($offset)
            ->take($amount)
            ->get()
            ->map(function($row) {
                return UserMapper::remap($row);
            });
    }


    public static function queryBadges($user_id) {
        return DB::connection(self::CONNECTION)
            ->table('user_badges')
            ->select('*')
            ->where('idUser', $user_id)
            ->get()
            ->map(function($row) {
                return BadgeMapper::remap($row);
            });
    }


    public static function queryAuths($user_id) {
        return DB::connection(self::CONNECTION)
            ->table('users_auths')
            ->select('*')
            ->where('idUser', $user_id)
            ->get();
    }

}