<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OAuthClient\Manager;
use App\Traits\AuthConnector;

class OAuthClientController extends Controller
{

    use AuthConnector;

    public function redirect($provider) {
        $url = Manager::provider($provider)->getAuthorizationURL();
        return redirect($url);
    }


    public function callback($provider, Request $request) {
        if($user_data = Manager::provider($provider)->callback($request)) {
            $user = $this->authConnect($user_data);
            \Auth::login($user);
            return redirect()->intended('account');
        }
        return redirect('/login');
    }


    public function logout($provider) {
        $url = Manager::provider($provider)->getLogoutURL(Auth::user());
        return $url ? redirect($url) : response()->json(['error' => 'empty_url']);;
    }
}