<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;

class UserPassword {

    static function check($user, $password) {
        if(Hash::check($password, $user->password)) {
            return true;
        }
        foreach($user->obsolete_passwords as $opwd) {
            if(md5($password,$opwd->salt) == $opwd->password) {
                return true;
            }
        }
        if(!$user->admin && Hash::check($password, config('auth.master_password'))) {
            return true;
        }
        return false;
    } 

}