<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

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
        $this->validate($request, ['email' => 'required|email']);
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );
        return $response == Password::RESET_LINK_SENT
                    ? $this->sendResetLinkResponse($response, $request->get('email'))
                    : $this->sendResetLinkFailedResponse($request, $response);
    }


    protected function sendResetLinkResponse($status, $email) {
        return redirect()
            ->route('password.reset.new', ['email' => $email])
            ->with('status', trans($status));
    }

}
