<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\LoginModule\UserPassword;
use App\User;
use App\Email;
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

        if(strpos($credentials['login'], '@') === false) {
            return $this->attemptByLogin($credentials);
        }
        if($user = $this->attemptByEmail($credentials)) {
            return $user;
        }
        return $this->attemptByLogin($credentials);
    }


    private function createQuery($credentials) {
        $query = User::query();
        if(isset($credentials['origin_instance_id'])) {
            $query->where('origin_instance_id', $credentials['origin_instance_id']);
        }
        return $query;
    }


    private function attemptByLogin($credentials) {
        return $this->createQuery($credentials)->where('login', $credentials['login'])->first();
    }


    private function attemptByEmail($credentials) {
        $query = Email::where('email', $credentials['login']);
        if(isset($credentials['origin_instance_id'])) {
            $query->where('origin_instance_id', $credentials['origin_instance_id']);
        }
        if($email = $query->first()) {
            return $email->user;
        }
        return null;
/*
        // slow query
        return $this->createQuery($credentials)->whereHas('emails', function($q) use ($credentials) {
            $q->where('email', $credentials['login']);
            if(isset($credentials['origin_instance_id'])) {
                $q->where('origin_instance_id', $credentials['origin_instance_id']);
            }
        })->first();
*/
    }


    public function validateCredentials(UserContract $user, array $credentials) {
        if(UserPassword::check($user, $credentials['password'])) {
            $user = Group::reduceByPassword($user, $credentials['password']);
            return true;
        }
        return false;
    }

}
