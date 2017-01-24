<?php

namespace App\Http\Controllers\OAuthServer;

class ScopeUserProfileController
{

    public function show() {
        $user = \App::user()->findOrFail(Authorizer::getResourceOwnerId());
        return response()->json($user);
    }

}
