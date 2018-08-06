<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\OAuthClient\Manager;
use App\LoginModule\Platform\PlatformContext;

class LogoutController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->middleware('auth', ['except' => ['getLogout']]);
        $this->context = $context;
    }


    public function getLogout(Request $request) {
        if(!Auth::check()) {
            return $this->redirectAfterLogout($request->get('redirect_uri'));
        }
        $request->session()->put('logout_redirect', $request->get('redirect_uri'));
        $active_connections = \Auth::user()->authConnections()->where('active', true)->whereIn('provider', Manager::SUPPORT_LOGOUT)->get();
        if(count($active_connections) == 0) {
            return $this->logoutFinish($request);
        }
        return view('auth.logout', [
            'active_connections' => $active_connections,
            'logout_config' => Auth::user()->logout_config ?: []
        ]);
    }


    public function logoutStart(Request $request) {
        $providers = $request->input('providers') ?: [];
        Auth::user()->logout_config = array_fill_keys($providers, 1);
        Auth::user()->save();
        if(count($providers)) {
            $request->session()->put('logout_providers', $providers);
            return redirect('logout_loop');
        }
        return redirect('logout_finish');
    }


    public function logoutFinish(Request $request) {
        $redirect_uri = $request->session()->pull('logout_redirect');
        Auth::guard()->logout();
        $context_data = $this->context->getData();
        $request->session()->flush();
        $request->session()->regenerate();
        $this->context->setData($context_data);
        return $this->redirectAfterLogout($redirect_uri);
    }


    public function logoutLoop(Request $request) {
        if($request->has('provider')) {
            if($connection = Auth::user()->authConnections()->where('provider', $request->get('provider'))->first()) {
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
        $logout_providers = session()->get('logout_providers');
        if(!is_array($logout_providers)) return false;
        return Auth::user()->authConnections()->where('active', true)->whereIn('provider', $logout_providers)->first();
    }


    private function redirectAfterLogout($redirect_uri) {
        if($redirect_uri) {
            return redirect($redirect_uri);
        } else {
            return redirect('/auth');
        }
    }

}