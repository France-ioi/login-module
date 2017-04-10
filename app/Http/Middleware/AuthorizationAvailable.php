<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Platform\PlatformRequest;
use Illuminate\Support\Facades\Auth;

class AuthorizationAvailable
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
        if(!PlatformRequest::profileFields()->filled()) {
            return redirect('/profile');
        }
        if(!PlatformRequest::profileFields()->verified()) {
            return redirect('/profile');
        }

        // Temporarily disable
        /*
        if(!PlatformRequest::badge()->verified()) {
            return redirect('/badge');
        }
        */

        return $next($request);
    }
}
