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

// Temporarily disable
/*        if(PlatformRequest::badge()->verified()) {
            return $next($request);
        }
        return redirect('/badge');*/
        return $next($request); // Delete when reenabling
    }
}
