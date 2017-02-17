<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\LoginModule\Platform\Platform;

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


    public function showLoginForm()
    {
        $auth_order = Platform::authOrder()->get();
        return view('auth.login', [
            'auth_visible' => $auth_order,
            'auth_hidden' => array_diff(config('auth.default_order'), $auth_order)
        ]);
    }


    public function showLoginEmailForm()
    {
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