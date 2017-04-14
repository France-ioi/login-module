<?php

namespace App\LoginModule;

use Auth;
use Session;
use Illuminate\Support\MessageBag;
use App\LoginModule\UserPassword;

class Reauthentication {


    const INTERVAL = 600; // sec
    const REDIRECT_URL_KEY = 'reauthentication_redirect_url';

    public static function required($request) {
        if(Auth::user()->has_password && time() - strtotime(Auth::user()->last_login) >= self::INTERVAL) {
            Session::put(self::REDIRECT_URL_KEY, $request->fullUrl());
            return true;
        }
        return false;
    }


    public static function update($password) {
        if(!empty($password) && UserPassword::check(Auth::user(), $password)) {
            Auth::user()->last_login = new \DateTime;
            Auth::user()->save();
            return redirect(self::getRedirectUrl());
        }
        $errors = new MessageBag([
            'password' => trans('auth.failed')
        ]);
        return redirect()->back()->withErrors($errors);
    }


    static function getRedirectUrl($alternative = '/account') {
        if(!$url = Session::pull(self::REDIRECT_URL_KEY)) {
            $url = $alternative;
        }
        return url($url);
    }

}