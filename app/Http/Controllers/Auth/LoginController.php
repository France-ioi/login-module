<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformRequest;
use App\OAuthClient\Manager;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('guest', ['except' => ['logout', 'getLogout']]);
    }


    public function username() {
        return 'login';
    }


    protected function authenticated(Request $request, $user) {
        $user->last_login = new \DateTime();
        $user->save();
        if($user->admin) {
            return redirect('/admin');
        }
    }


    public function showLoginForm() {
        $auth_order = PlatformRequest::authOrder()->get();
        $default_order = array_merge(['login'], Manager::providers());
        if($auth_order) {
            $auth_visible = $auth_order;
            $auth_hidden = array_diff($default_order, $auth_order);
        } else {
            $auth_visible = $default_order;
            $auth_hidden = [];
        }
        return view('auth.login', [
            'auth_visible' => $auth_visible,
            'auth_hidden' => $auth_hidden,
            'badge_required' => PlatformRequest::badge()->url()
        ]);
    }


    public function showLoginEmailForm() {
        return view('auth.login_email');
    }


    public function getLogout(Request $request) {
        if(!\Auth::check()) {
            return $this->getRedirectAfterLogout($request);
        }
        return view('auth.logout', [
            'redirect_uri' => $request->get('redirect_uri'),
            'active_connections' => \Auth::user()->auth_connections()->where('is_active', 1)->get()
        ]);
    }


    public function logout(Request $request) {
        //TODO: active connections
        $this->guard()->logout();
        $request->session()->flush();
        $request->session()->regenerate();
        return $this->getRedirectAfterLogout($request);
    }


    private function getRedirectAfterLogout($request) {
        if($request->has('redirect_uri')) {
            return redirect($request->input('redirect_uri'));
        } else {
            return redirect(route('login'));
        }
    }

}