<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\OAuthClient\Manager;
use App\LoginModule\AuthConnector;
use Session;
use Auth;

class OAuthClientController extends Controller
{


    public function redirect($provider) {
        $url = Manager::provider($provider)->getAuthorizationURL();
        return redirect($url);
    }


    public function preferences($provider) {
        $auth_connection = Auth::user()->auth_connections()->where('provider', $provider)->firstOrFail();
        $url = Manager::provider($provider)->getPreferencesURL($auth_connection);
        return redirect($url);
    }


    public function callback($provider, Request $request) {
        $user_was_logged = \Auth::check();
        if($auth = Manager::provider($provider)->callback($request)) {
            $auth['provider'] = $provider;
            if($user = AuthConnector::connect($auth)) {
                //TODO: check user group here
                if($user_was_logged) {
                    return redirect('/auth_methods');
                }
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
