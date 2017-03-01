<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Session;
use App\LoginModule\Platform\PlatformRequest;

class PlatformEmailVerified
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

        if(PlatformRequest::profileFields()->verified()) {
            return $next($request);
        }
        return redirect('/email_verification');
    }
}
