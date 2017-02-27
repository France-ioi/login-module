<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
use App\LoginModule\Platform\PlatformRequest;

class PlatformBadgeVerified
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

        if(PlatformRequest::badge()->verified()) {
            return $next($request);
        }
        Session::put('url.intended', $request->fullUrl());
        return redirect('/badge');
    }
}
