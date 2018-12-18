<?php

namespace App\Http\Middleware;

use Closure;

class UserHelperMiddleware
{

    public function handle($request, Closure $next) {
        $user = $request->user();
        if(!$user->can('admin.user_helper') || !$user->userHelper) {
            abort(403);
        }
        return $next($request);
    }
}
