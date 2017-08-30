<?php

namespace App\LoginModule\Migrators\Merge;

use App\User;
use App\OfficialDomain;
use App\Client;

class Data
{

    public static function queryUsers($connection, $offset, $amount) {
        return User::on($connection->getName())
            ->with(['emails', 'badges', 'auth_connections', 'obsolete_passwords'])
            ->where('admin', 0)
            ->skip($offset)
            ->take($amount)
            ->get();
    }


    public static function queryOfficialDomains($connection, $offset, $amount) {
        return OfficialDomain::on($connection->getName())
            ->skip($offset)
            ->take($amount)
            ->get();
    }


    public static function queryOAuthClients($connection) {
        return Client::on($connection->getName())->get();
    }

}