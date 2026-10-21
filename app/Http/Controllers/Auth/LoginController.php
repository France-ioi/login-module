<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
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
    protected $redirectTo = '/profile';

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


    public function attemptLogin(Request $request)
    {
        $credentials = $this->credentials($request);
        $credentials['origin_instance_id'] = null;
        $remember = $request->filled('remember');
        if($res = $this->guard()->attempt($credentials, $remember)) {
            return $res;
        }
        \App\OriginInstance::get()->pluck('id')->map(function($id) use ($credentials, $remember) {
            $credentials['origin_instance_id'] = $id;
            if($res = $this->guard()->attempt($credentials, $remember)) {
                return $res;
            }
        });
        return false;
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
        //check group here
        if($user->hasRole('admin')) {
            return redirect('/admin');
        }
        return redirect($this->context->continueUrl());
    }


    public function showLoginForm() {
        $client = $this->context->client();
        return view('auth.login', [
            'platform_name' => $client ? $client->name : trans('app.name')
        ]);
    }


}