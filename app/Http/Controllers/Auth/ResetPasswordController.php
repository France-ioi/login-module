<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showNewPasswordForm(Request $request) {
        return view('auth.passwords.reset', [
            'token' => $request->get('token'),
            'email' => $request->get('email')
        ]);
    }


    protected function resetPassword($email, $password) {
        $email->user->forceFill([
            'password' => bcrypt($password),
            'remember_token' => str_random(60),
        ])->save();
        $email->user->obsolete_passwords()->delete();
        $this->guard()->login($email->user);
    }

}