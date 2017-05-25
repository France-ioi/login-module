<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use App\Email;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function sendResetLinkEmail(Request $request) {
        if($request->has('email_id')) {
            $credentials = $request->only('email_id');
        } else {
            $this->validate($request, ['login_or_email' => 'required']);
            $credentials = [
                'email' => $request->input('login_or_email')
            ];
            if(strpos($credentials['email'], '@') === false) {
                // its login
                return redirect()->route('passwords.emails', $credentials['email']);
            }
        }
        $response = $this->broker()->sendResetLink($credentials);
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response, $request->get('email'))
                    : $this->sendResetLinkFailedResponse($request, $response);
    }


    protected function sendResetLinkResponse($status, $email) {
        return redirect()
            ->route('password.reset.new', ['email' => $email])
            ->with('status', trans($status));
    }


    public function showEmails($login) {
        $emails = Email::whereHas('user', function($q) use ($login) {
            return $q->where('login', $login);
        })->get();
        if(!count($emails)) {
            return redirect()->route('password.request')
                ->with('status', trans('passwords.user'));
        }
        return view('auth.passwords.emails_list', [
            'emails' => $emails
        ]);
    }


}