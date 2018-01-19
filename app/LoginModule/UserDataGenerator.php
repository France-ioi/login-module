<?php

namespace App\LoginModule;

use App\User;
use App\Badge;
use App\AutoLoginToken;

class UserDataGenerator {


    public function login($prefix = '') {
        do {
            $login = $prefix.$this->randomStr();
        } while (User::where('login', $login)->first());
        return $login;
    }


    public function loginFromBadge($badge_user, $prefix = '') {
        //badge_[firstname][first letter of lastname][3 digits]
        $first_name = array_get($badge_user, 'first_name');
        $first_name = preg_replace('/[^A-Za-z]/', '', $first_name);
        $last_name = array_get($badge_user, 'last_name');
        $last_name = preg_replace('/[^A-Za-z]/', '', $last_name);
        if($first_name != '' && $last_name != '') {
            do {
                $login = $prefix.strtolower($first_name.$last_name).$this->randomNumber(3);
            } while (User::where('login', $login)->first());
            return $login;
        }
        return $this->login($prefix);
    }


    public function password() {
        return $this->randomStr();
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