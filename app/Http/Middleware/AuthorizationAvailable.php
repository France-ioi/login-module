<?php

namespace App\Http\Middleware;

use Closure;
use App\LoginModule\Profile\UserProfile;
use App\LoginModule\Profile\Verification\Verificator;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Migrators\Merge\Group;

class AuthorizationAvailable
{

    protected $profile;
    protected $verificator;
    protected $context;

    public function __construct(UserProfile $profile, Verificator $verificator, PlatformContext $context) {
        $this->context = $context;
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
        $user = $request->user();
        $completed = $this->profile->completed($user);
        $verified = $this->verificator->verify($user) === true;
        $revalidated = !Group::revalidationRequired($user);
        if(!$completed || !$verified || !$revalidated || $user->login_change_required) {
            return redirect('/profile');
        }
        if(!$this->context->badge()->valid()) {
            return redirect('/badge');
        }
        return $next($request);
    }
}
