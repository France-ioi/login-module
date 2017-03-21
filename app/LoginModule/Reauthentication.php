<?php

namespace App\LoginModule;

use Auth;
use Session;
use Illuminate\Support\MessageBag;

class Reauthentication {


    const INTERVAL = 600; // sec
    const REDIRECT_URL_KEY = 'reauthentication_redirect_url';

    public static function required($request) {
        if(!is_null(Auth::user()->password) && time() - strtotime(Auth::user()->last_login) >= self::INTERVAL) {
            Session::put(self::REDIRECT_URL_KEY, $request->fullUrl());
            return true;
        }
        return false;
    }


    public static function update($password) {
        if(!empty($password) && Auth::user()->password == md5($password)) {
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