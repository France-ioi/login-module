<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\LoginModule\UserPassword;
use App\User;
use App\LoginModule\Migrators\Merge\Group;

class UserProvider extends EloquentUserProvider
{
    public function retrieveByCredentials(array $credentials) {
         if(empty($credentials)) {
             return;
         }
         // for pwd restore, it available for users with emails only
         if(isset($credentials['email'])) {
             $credentials['login'] = $credentials['email'];
             unset($credentials['email']);
         }

        $query = User::query();
        if(strpos($credentials['login'], '@') === false) {
            $query->where('login', $credentials['login']);
        } else {
            $query->whereHas('emails', function($q) use($credentials) {
                $q->where('email', $credentials['login']);
                if(isset($credentials['origin_instance_id'])) {
                    $q->where('origin_instance_id', $credentials['origin_instance_id']);
                 }
            });
        }
         if(isset($credentials['origin_instance_id'])) {
            $query->where('origin_instance_id', $credentials['origin_instance_id']);
         }
        return $query->first();
    }


    public function validateCredentials(UserContract $user, array $credentials) {
        if(UserPassword::check($user, $credentials['password'])) {
            $user = Group::reduceByPassword($user, $credentials['password']);
            return true;
        }
        return false;
    }

}
