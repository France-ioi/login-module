<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

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

        $query = $this->createModel()->newQuery();

        if(strpos($credentials['login'], '@') === false) {
            $query->where('login', $credentials['login']);
        } else if($email = \App\Email::where('email', $credentials['login'])->first()) {
            $query->where('id', $email->user_id);
        } else return;

        return $query->first();
    }


    public function validateCredentials(UserContract $user, array $credentials) {
        $plain = $credentials['password'];
        if($this->hasher->check($plain, $user->getAuthPassword())) {
            $user->last_login = new \DateTime();
            $user->ip = \Request::ip();
            $user->save();
            return true;
        }

        // todo: ask why md5? :)
        $master = config('auth.master_hash_md5');
        return !empty($master) && $master === md5($plain);
    }

}