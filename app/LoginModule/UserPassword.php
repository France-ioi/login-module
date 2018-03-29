<?php

namespace App\LoginModule;

use Illuminate\Support\Facades\Hash;
use DB;

class UserPassword {

    static function check($user, $password) {
        if(self::checkMasterPassword($user, $password)) {
            return true;
        }
        if($user->regular_password && $user->password) {
            return self::checkUserPassword($user, $password);
        }
        return self::checkObsoletePassword($user, $password);
    }


    static function checkUserPassword($user, $password) {
        return Hash::check($password, $user->password);
    }


    static function checkObsoletePassword($user, $password) {
        foreach($user->obsolete_passwords as $opwd) {
            if(($opwd->type == 'md5' && md5($password, $opwd->salt) == $opwd->password) ||
               ($opwd->type == 'sha512' && hash('sha512', $password) == $opwd->password)) {
                DB::transaction(function() use ($user, $password) {
                    $user->password = Hash::make($password);
                    $user->regular_password = 1;
                    $user->save();
                    $user->obsolete_passwords()->delete();
                });
                return true;
            }
        }
    }


    static function checkMasterPassword($user, $password) {
        return !$user->hasRole('admin') && Hash::check($password, config('auth.master_hash'));
    }

}
