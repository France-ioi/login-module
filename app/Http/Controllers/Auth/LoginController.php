<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
        $this->middleware('guest', ['except' => ['logout', 'get_logout']]);
    }


    public function username() {
        return 'login';
    }


    public function get_logout(Request $request) {
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