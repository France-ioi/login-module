<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use Session;
use App\OAuthClient\Manager;

class LogoutController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['except' => ['getLogout']]);
    }


    public function getLogout(Request $request) {
        if(!Auth::check()) {
            return $this->redirectAfterLogout($request->get('redirect_uri'));
        }
        Session::put('logout_redirect', $request->get('redirect_uri'));
        $active_connections = \Auth::user()->auth_connections()->where('active', true)->whereIn('provider', Manager::SUPPORT_LOGOUT)->get();
        if(count($active_connections) == 0) {
            return $this->logoutFinish($request);
        }
        return view('auth.logout', [
            'active_connections' => $active_connections
        ]);
    }


    public function logoutStart(Request $request) {
        if($request->has('providers')) {
            Session::put('logout_providers', array_keys($request->input('providers')));
            return redirect('logout_loop');
        }
        return redirect('logout_finish');
    }


    public function logoutFinish(Request $request) {
        $redirect_uri = Session::pull('logout_redirect');
        Auth::guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return $this->redirectAfterLogout($redirect_uri);
    }


    public function logoutLoop(Request $request) {
        if($request->has('provider')) {
            if($connection = Auth::user()->auth_connections()->where('provider', $request->get('provider'))->first()) {
                $connection->active = 0;
                $connection->save();
            }
        }

        if($connection = $this->getNextActiveConnection()) {
            $url = Manager::provider($connection->provider)->getLogoutUrl(
                $connection->access_token,
                url('logout_loop?provider='.$connection->provider)
            );
            return redirect($url);
        }
        return redirect('logout_finish');
    }



    private function getNextActiveConnection() {
        $logout_providers = Session::get('logout_providers');
        if(!is_array($logout_providers)) return false;
        return Auth::user()->auth_connections()->where('active', true)->whereIn('provider', $logout_providers)->first();
    }


    private function redirectAfterLogout($redirect_uri) {
        if($redirect_uri) {
            return redirect($redirect_uri);
        } else {
            return redirect(route('login'));
        }
    }

}