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
        $auth_connection = Auth::user()->authConnections()->where('provider', $provider)->firstOrFail();
        $url = Manager::provider($provider)->getPreferencesURL($auth_connection);
        return redirect($url);
    }


    public function callback($provider, Request $request) {
        //TODO refactoring: return redirect from AuthConnector::connect
        $user_was_logged = \Auth::check();
        if($auth = Manager::provider($provider)->callback($request)) {
            $auth['provider'] = $provider;
            if($user = AuthConnector::connect($auth)) {
                //TODO: check user group here???
                if($user_was_logged) {
                    $pms_profile_callback = session()->pull('pms_profile_callback', 1);
                    return $pms_profile_callback ? redirect('/profile') : redirect('/auth_methods');
                }
                return redirect()->intended('/auth_methods');
            }
            return redirect('/oauth_client/email_exists');
        }
        return redirect('/session_expired');
    }


    public function sessionExpired() {
        return view('auth.expired');
    }


    public function emailExists() {
        if($data = session()->pull('auth_connection_exists')) {
            $data['login'] = $data['login'] ? $data['login'] : trans('auth_connections.email_exists_login_empty');
            return view('oauth_client.email_exists', $data);
        }
        return redirect('/auth');
    }


    public function remove($provider) {
        if(array_search($provider, Manager::SUPPORT_REMOVE) !== false) {
            AuthConnector::disconnect($provider);
        }
        return redirect('/auth_methods');
    }
}
