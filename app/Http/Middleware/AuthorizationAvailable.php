<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Profile\UserProfile;

class AuthorizationAvailable
{

    protected $profile;


    public function __construct(UserProfile $profile) {
        $this->profile = $profile;
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
        if(!$this->profile->completed() && !$this->profile->verified()) {
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
