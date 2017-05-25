<?php

namespace App\Helpers;

class EmailMasker {

    static function mask($email) {
        list($user, $domain) = explode('@', $email);
        $len = strlen($user);
        if(strlen($user) < 3) {
            $mask = str_repeat('*', $len);
        } else {
            $mask = substr($user, 0, 1).str_repeat('*', $len - 2).substr($user, -1);
        }
        return $mask.'@'.$domain;
        
    }

}