<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Reauthentication;

class CheckReauthentication
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if(Reauthentication::required($request)) {
            return redirect('/reauthentication');
        }
        return $next($request);
    }
}
