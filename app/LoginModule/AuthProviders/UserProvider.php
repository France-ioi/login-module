<?php

namespace App\LoginModule\AuthProviders;

use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable as UserContract;
use App\LoginModule\UserPassword;
use Illuminate\Support\Facades\DB;
use App\LoginModule\AuthConnector;
use App\Email;
use App\User;
use App\AuthConnection;
use App\ObsoletePassword;

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
        return UserPassword::check($user, $credentials['password']);
    }

}
