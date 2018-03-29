<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verification;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Migrators\Merge\Group;

class AuthorizationAvailable
{

    protected $profile;
    protected $verification;
    protected $context;

    public function __construct(UserProfile $profile, Verification $verification, PlatformContext $context) {
        $this->context = $context;
        $this->profile = $profile;
        $this->verification = $verification;
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
        $user = $request->user();
        $completed = $this->profile->completed($user);
        $revalidated = !Group::revalidationRequired($user);
        if(!$completed || !$revalidated || $user->login_change_required) {
            return redirect('/profile');
        }
        if(!$this->verification->authReady($user)) {
            return redirect('/verification');
        }
        if(!$this->context->badge()->valid()) {
            return redirect('/badge');
        }
        return $next($request);
    }
}
