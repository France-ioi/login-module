<?php

namespace App\LoginModule;

use App\User;
use App\Badge;
use App\AutoLoginToken;

class UserDataGenerator {


    public function login($prefix = '', $postfix_length = 6) {
        $cnt = 0;
        do {
            $login = $prefix.$this->randomStr($postfix_length);
            // something wrong, avoid infinite loop
            if(++$cnt > 100) return null;
        } while (User::where('login', $login)->first());
        return $login;
    }


    public function batchLogins($amount, $prefix = '', $postfix_length = 6) {
        $logins = [];
        $attempts = 0;
        do {
            if(++$attempts > 100) return null;

            // fill logins
            $cnt = $amount - count($logins);
            for($i=0; $i<$cnt; $i++) {
                do {
                    $login = $prefix.$this->randomStr($postfix_length);
                } while (array_search($login, $logins) !== false);
                $logins[] = $login;
            }


            // remove existent
            $exists = User::whereIn('login', $logins)->get()->toArray();
            $logins = array_diff($logins, $exists);
        } while(count($logins) < $amount);
        return $logins;
    }


    public function loginFromBadge($badge_user, $prefix = '') {
        //badge_[firstname][first letter of lastname][3 digits]
        $first_name = array_get($badge_user, 'first_name');
        $first_name = preg_replace('/[^A-Za-z]/', '', $first_name);
        $last_name = array_get($badge_user, 'last_name');
        $last_name = preg_replace('/[^A-Za-z]/', '', $last_name);
        if($first_name != '' && $last_name != '') {
            $cnt = 0;
            do {
                $login = $prefix.strtolower($first_name.$last_name).$this->randomNumber(3);
                // something wrong, avoid infinite loop
                if(++$cnt > 100) return null;
            } while (User::where('login', $login)->first());
            return $login;
        }
        return $this->login($prefix);
    }


    public function password($l) {
        return $this->randomStr($l);
    }


    public function autoLoginToken() {
        do {
            $token = $this->randomStr(50);
        } while (AutoLoginToken::where('token', $token)->first());
        return $token;
    }


    public function participationCode() {
        do {
            $code = $this->randomStr(10);
        } while (Badge::where('code', $code)->where('url', '')->first());
        return $code;
    }


    private function randomStr($l = 10) {
        $c = '23456789abcdefghijkmnpqrstuvwxyz';
        return substr(str_shuffle(str_repeat($c, 5)), 0, $l);
    }


    private function randomNumber($l = 3) {
        $c = '0123456789';
        return substr(str_shuffle(str_repeat($c, 5)), 0, $l);
    }

}