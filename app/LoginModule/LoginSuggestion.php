<?php

namespace App\LoginModule;

use App\User;

class LoginSuggestion
{

    public function get($login) {
        $login = $this->normalize($login);
        $first = true;
        while($this->exists($login)) {
            $login .= rand($first ? 1 : 0, 9);
            $first = false;
        }
        return $login;
    }


    private function normalize($login) {
        $login = strtolower($login);
        $login = preg_replace(config('profile.login_validator.filter'), '', $login);
        if($login == '') {
            $login = $this->generateLogin();
        }
        return $login;
    }


    private function generateLogin() {
        return 'user'.rand(100, 999);
    }


    private function exists($login) {
        $q = User::where('login', $login);
        $user = auth()->user();
        if($user) {
            $q->where('id', '<>', $user->id);
        }
        return !!$q->first();
    }

}
