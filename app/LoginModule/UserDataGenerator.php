<?php

namespace App\LoginModule;

use App\User;

class UserDataGenerator {


    public function login($prefix = '') {
        do {
            $login = $prefix.$this->randomStr();
        } while (User::where('login', $login)->first());
        return $login;
    }


    public function password() {
        return $this->randomStr();
    }


    public function autoLoginToken() {
        return $this->randomStr(50);
    }


    private function randomStr($l = 10) {
        $c = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($c, 5)), 0, $l);
    }

}