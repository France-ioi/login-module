<?php

namespace App\Http\Middleware;

use Closure;
use Auth;
use App\LoginModule\Migrators\Merge\Group;

class MergingAccounts
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
        $user = Auth::guard($guard)->user();
        if(Group::mergingRequired($user)) {
            return redirect('/merging_accounts');
        }
        return $next($request);
    }
}
