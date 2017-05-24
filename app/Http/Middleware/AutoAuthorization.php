<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Platform\PlatformContext;
use Illuminate\Support\Facades\Auth;

class AutoAuthorization
{

    private $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if($client = $this->context->client()) {
            $pms_active = (bool) Auth::guard($guard)->user()->auth_connections()->where('provider', 'pms')->where('active', '1')->first();
            if($client->autoapprove_authorization && $pms_active) {
                $url = str_replace('/oauth/authorize?', '/oauth/auto_authorize?', $request->fullUrl());
                return redirect($url);
            }
        }
        return $next($request);
    }

}
