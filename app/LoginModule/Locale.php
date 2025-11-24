<?php

namespace App\LoginModule;

use Auth;
use Session;

class Locale
{

    const SESSION_KEY = 'locale';


    public static function get($request = null) {
        $locale = '';
        if(Auth::check()) {
            $locale = Auth::user()->language;
        }
        if(!$locale && Session::has(self::SESSION_KEY)) {
            $locale = Session::get(self::SESSION_KEY);
        }
        if(!$locale && $request !== null) {
            $locale = $request->getPreferredLanguage(array_keys(config('app.locales')));
        }
        return self::validate($locale);
    }


    public static function set($locale) {
        $locale = self::validate($locale);
        if(Auth::check()) {
            Auth::user()->language = $locale;
            Auth::user()->save();
        }
        Session::put(self::SESSION_KEY, $locale);
    }


    public static function validate($locale) {
        $locale = strtolower($locale);
        $locales = config('app.locales');
        return isset($locales[$locale]) ? $locale : config('app.locale');
    }

}