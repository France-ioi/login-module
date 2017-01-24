<?php

namespace App\Http\Controllers\OAuthServer;

use Authorizer;
use Request;

class AccessTokenController
{

    public function issue() {
        return response()->json(Authorizer::issueAccessToken());
    }

}
