<?php

namespace App\Http\Middleware;

use Closure;
use App\Traits\ProfileCompletion;
use Illuminate\Support\Facades\Auth;

class ProfileCompleted
{

    use ProfileCompletion;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $fields = $this->getProfileEmptyFields(Auth::guard($guard)->user());
        if(count($fields) > 0) {
            session()->put('url.intended', $request->fullUrl());
            return redirect('/profile');
        }
        return $next($request);
    }
}
