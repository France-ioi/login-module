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


    public function loginFromBadge($badge_user, $prefix = '') {
        $first_name = array_get($badge_user, 'first_name');
        $first_name = preg_replace('/[^A-Za-z]/', '', $first_name);
        $first_name = substr($first_name, 0, 10);
        $last_name = array_get($badge_user, 'last_name');
        $last_name = preg_replace('/[^A-Za-z]/', '', $last_name);
        $last_name = substr($last_name, 0, 10);
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
        } while (Badge::where('code', $code)->where('url', '')->whereNull('badge_api_id')->first());
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