<?php

namespace App\Http\Middleware;

use Closure;

class UserHelperMiddleware
{

    public function handle($request, Closure $next) {
        $user = $request->user();
        if(!$user->can('admin.user_helper')) {
            abort(403);
        }
        if(!$user->userHelper) {
            return redirect('admin/user_helper_messages/missed_settings');
        }
        return $next($request);
    }
}
