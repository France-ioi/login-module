<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OAuthClient\Manager;
use App\LoginModule\AuthConnector;
use Session;

class OAuthClientController extends Controller
{


    public function redirect($provider) {
        $url = Manager::provider($provider)->getAuthorizationURL();
        return redirect($url);
    }


    public function preferences($provider) {
        $url = Manager::provider($provider)->getPreferencesURL();
        return redirect($url);
    }


    public function callback($provider, Request $request) {
        if($auth = Manager::provider($provider)->callback($request)) {
            $auth['provider'] = $provider;
            if(AuthConnector::connect($auth)) {
                return redirect()->intended('/auth_methods');
            }
            Session::put('auth_connection', $auth);
            return redirect('/oauth_client/email_exists');
        }
        return redirect('/session_expired');
    }


    public function sessionExpired() {
        return view('auth.expired');
    }


    public function emailExists() {
        if($auth_connection = Session::pull('auth_connection')) {
            return view('oauth_client.email_exists', $auth_connection);
        }
        return redirect()->route('login');
    }


    public function remove($provider) {
        if(array_search($provider, Manager::SUPPORT_REMOVE) !== false) {
            AuthConnector::disconnect($provider);
        }
        return redirect('/auth_methods');
    }
}
