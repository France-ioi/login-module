<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Locale;
use App;

class AppLocale
{


    public function handle($request, Closure $next) {
        App::setLocale(Locale::get($request));
        return $next($request);
    }


}
