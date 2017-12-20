<?php

namespace App\Http\Middleware;

use Closure;
use Auth;

class AutoLoginToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($request->has('login') && $request->has('auto_login_token')) {
            $auto_login = \App\AutoLoginToken::where('token', $request->get('auto_login_token'))->first();
            if($auto_login && $auto_login->user->login == $request->get('login')) {
                Auth::guard($guard)->login($auto_login->user);
                $auto_login->delete();
            }
        }
        return $next($request);
    }


    private function auth($login, $token) {

    }


}
