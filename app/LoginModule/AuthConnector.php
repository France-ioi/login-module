<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\Email;
use App\AuthConnection;

class AuthConnector
{


    static function connect($user_data) {
        if($connection = AuthConnection::where('uid', $user_data['uid'])->where('provider', $user_data['provider'])->with('user')->first()) {
            $connection->user->update($user_data);
            $connection->is_active = true;
            $connection->save();
            return $connection->user;
        }

        if($email = Email::where('email', $user_data['email'])->first()) {
            return $email->user;
        }

        $user = User::create([
            'first_name' => $user_data['first_name'],
            'last_name' => $user_data['last_name']
        ]);
        $connection = new AuthConnection($user_data);
        $connection->is_active = true;
        $user->auth_connections()->save($connection);

        if($user_data['email']) {
            $user->emails()->save(new Email([
                'role' => 'primary',
                'email' => $user_data['email']
            ]));
        }

        return $user;
    }

}