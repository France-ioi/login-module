<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verification;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Migrators\Merge\Group;
use App\LoginModule\Profile\ProfileFilter;



class AuthorizationAvailable
{

    protected $profile;
    protected $filter;
    protected $verification;
    protected $context;


    public function __construct(UserProfile $profile,
                                ProfileFilter $filter,
                                Verification $verification,
                                PlatformContext $context) {
        $this->context = $context;
        $this->profile = $profile;
        $this->verification = $verification;
        $this->filter = $filter;
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
        $this->context->linkUser($user);        
        $completed = $this->profile->completed($user);
        $revalidated = !Group::revalidationRequired($user);
        $filter_passed = $this->filter->pass($user);
        if(!$completed || !$revalidated || !$filter_passed || $user->login_change_required) {
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
