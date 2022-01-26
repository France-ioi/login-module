<?php

namespace App\LoginModule;

class Locale
{

    const SESSION_KEY = 'locale';

    public static function get() {
        if($locale = self::detectSessionLocale()) {
            return $locale;
        }
        if($locale = self::detectUserLocale()) {
            return $locale;
        }        
        if($locale = self::detectBrowserLocale()) {
            return $locale;
        }
        return config('app.locale');
    }


    public static function set($locale) {
        $locale = self::validate($locale);
        session()->put(self::SESSION_KEY, $locale);
        return $locale;
    }


    public static function setIfEmpty($locale) {
        if(!session()->has(self::SESSION_KEY)) {
            return self::set($locale);
        }
        return false;
    }    


    public static function validate($locale) {
        $locale = strtolower($locale);
        $locales = config('app.locales');
        return isset($locales[$locale]) ? $locale : config('app.locale');
    }



    // locale value sources
    private static function detectUserLocale() {
        if(auth()->check()) {
            $locale = auth()->user()->language;        
            if(self::validate($locale)) {
                return $locale;
            }
        }
        return false;            
    }

    private static function detectSessionLocale() {
        if(session()->has(self::SESSION_KEY)) {
            $locale = session()->get(self::SESSION_KEY);
            if(self::validate($locale)) {
                return $locale;
            }
        }
        return false;            
    }

    private static function detectBrowserLocale() {
        $locale = request()->server('HTTP_ACCEPT_LANGUAGE');
        if(!$locale) {
            return false;
        }
        $locale = substr($locale, 0, 2);
        $locale = strtolower($locale);
        if(self::validate($locale)) {
            return $locale;
        }
        return false;
    }
}