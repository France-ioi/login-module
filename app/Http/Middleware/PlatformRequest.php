<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Platform\PlatformRequest as PlatformRequestLib;

class PlatformRequest
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
        PlatformRequestLib::cacheToSession($request);
        return $next($request);
    }
}
