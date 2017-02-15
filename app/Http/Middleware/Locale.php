<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Locale
{

    public function __construct() {
        $this->locales = config('app.locales');
    }



    public function handle($request, Closure $next, $guard = null) {
        if(Auth::guard($guard)->check()) {
            $locale = Auth::guard($guard)->user()->language;
        } else {
            $locale = session('locale');
        }
        $locale = $this->checkLocale($locale);
        app()->setLocale($locale);
        return $next($request);
    }


    private function checkLocale($locale) {
        return isset($this->locales[$locale]) ? $locale : config('app.locale');
    }

}
