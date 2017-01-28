<?php

namespace App\Http\Controllers\OAuthServer;

use Authorizer;
use App\User;

class UserProfileController
{

    public function show() {
        $user = User::with('badges')->findOrFail(Authorizer::getResourceOwnerId());
        return response()->json($user);
    }

}
