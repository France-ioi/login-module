<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\Email;
use App\AuthConnection;
use Auth;

class AuthConnector
{


    static function connect($auth) {
        if($connection = self::findConnection($auth)) {
            Auth::login($connection->user);
            $connection->active = true;
            $connection->save();
            $user = $connection->user;
        } else {
            $connection = new AuthConnection($auth);
            $connection->active = true;
            if(Auth::check()) {
                $user = Auth::user();
                $user->auth_connections()->save($connection);
            } else {
                if(isset($auth['email']) && Email::where('email', $auth['email'])->first()) {
                    return false;
                }
                $user = User::create($auth);
                $user->auth_connections()->save($connection);
                if(isset($auth['email'])) {
                    $user->emails()->save(new Email([
                        'role' => 'primary',
                        'email' => $auth['email'],
                        'verified' => true
                    ]));
                }
                Auth::login($user);
            }
        }
        return $user;
    }


    static function findConnection($auth) {
        // replace old google id
        if(isset($auth['uid_old']) && $auth['provider'] == 'google') {
            if($connection = AuthConnection::where('uid', $auth['uid_old'])->where('provider', $auth['provider'])->first()) {
                $connection->uid = $auth['uid'];
                $connection->save();
                return $connection;
            }
        }
        return AuthConnection::where('uid', $auth['uid'])->where('provider', $auth['provider'])->first();
    }


    static function disconnect($provider) {
        if(Auth::check()) {
            Auth::user()->auth_connections()->where('provider', $provider)->delete();
        }
    }

}