<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use App\OAuthClient\Manager;
use App\LoginModule\Platform\PlatformContext;

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
        $this->middleware('guest', ['except' => ['logout']]);
    }


    public function username() {
        return 'login';
    }


    protected function authenticated(Request $request, $user) {
        if($user->admin) {
            return redirect('/admin');
        }
    }


    public function showLoginForm(PlatformContext $context) {
        $auth_order = [];
        $badge_required = false;
        if($client = $context->client()) {
            $auth_order =  is_array($client->auth_order) ? $client->auth_order : [];
            $badge_required = (bool) $context->badge()->url();
        }
        $default_order = array_merge(['login'], Manager::providers());
        if($auth_order) {
            $auth_visible = $auth_order;
            $auth_hidden = array_diff($default_order, $auth_order);
        } else {
            $auth_visible = $default_order;
            $auth_hidden = [];
        }
        return view('auth.login', compact('auth_visible', 'auth_hidden', 'badge_required'));
    }


    public function showLoginEmailForm() {
        return view('auth.login_email');
    }

}