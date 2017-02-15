<?php

namespace App\LoginModuleAuth;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;

class UserProvider extends EloquentUserProvider
{


    public function retrieveByCredentials(array $credentials) {
        if(empty($credentials)) {
            return;
        }

        // pwd restore available for users with emails only
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

}