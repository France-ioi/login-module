<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\AuthConnection;

trait AuthConnector
{


    private function authConnect($user_data) {
        if(empty($user_data['email'])) {
            $user_data['email'] = $user_data['provider'].$user_data['uid'].'@'.$_SERVER['SERVER_NAME']; // facebook user email may be empty
        }

        if($connection = AuthConnection::where('uid', $user_data['uid'])->where('provider', $user_data['provider'])->with('user')->first()) {
            $connection->user->update($user_data);
            $connection->is_active = true;
            $connection->save();
            return $connection->user;
        }

        $user = User::where('email', $user_data['email'])->first();
        if(!$user) {
            $user_data['password'] = Hash::make(str_random());
            $user = User::create($user_data);
            $connection = new AuthConnection($user_data);
            $connection->is_active = true;
            $user->auth_connections()->save($connection);
        }
        return $user;
    }

}