<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verificator;


class AuthorizationAvailable
{

    protected $profile;
    protected $verificator;

    public function __construct(UserProfile $profile, Verificator $verificator) {
        $this->profile = $profile;
        $this->verificator = $verificator;
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
        if(!$this->profile->completed($request->user()) || $this->verificator->verify($request->user()) !== true) {
            return redirect('/profile');
        }
        // Temporarily disable
        /*
        if(!$this->context->badge()->verified()) {
            return redirect('/badge');
        }
        */
        return $next($request);
    }
}
