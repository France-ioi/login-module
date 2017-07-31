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

    protected $context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PlatformContext $context) {
        $this->middleware('guest', ['except' => ['logout']]);
        $this->context = $context;
    }


    public function username() {
        return 'login';
    }


    protected function sendLoginResponse(Request $request) {
        $context_data = $this->context->getData();
        $request->session()->regenerate();
        $this->context->setData($context_data);
        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user())
                ?: redirect()->intended($this->redirectPath());
    }


    protected function authenticated(Request $request, $user) {
        if($user->admin) {
            return redirect('/admin');
        }
        return redirect($this->context->continueUrl('/account'));
    }


    public function showLoginForm() {
        $auth_order = [];
        $badge_required = false;
        if($client = $this->context->client()) {
            $auth_order =  is_array($client->auth_order) ? $client->auth_order : [];
            $badge_required = (bool) $this->context->badge()->url();
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