<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;
use DB;

class UserPassword {

    static function check($user, $password) {
        if(self::checkMasterPassword($user, $password)) {
            return true;
        }
        if($user->regular_password) {
            return self::checkUserPassword($user, $password);
        }
        return self::checkObsoletePassword($user, $password);
    }


    static function checkUserPassword($user, $password) {
        return Hash::check($password, $user->password);
    }


    static function checkObsoletePassword($user, $password) {
        foreach($user->obsolete_passwords as $opwd) {
            if(md5($password,$opwd->salt) == $opwd->password) {
                DB::transaction(function() use ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->save();
                    $user->obsolete_passwords()->delete();
                });
                return true;
            }
        }
    }


    static function checkMasterPassword($user, $password) {
        return !$user->admin && Hash::check($password, config('auth.master_hash'));
    }

}