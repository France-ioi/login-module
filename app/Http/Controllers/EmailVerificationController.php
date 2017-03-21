<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\LoginModule\EmailVerification\Verificator;
use App\LoginModule\Platform\PlatformRequest;

class EmailVerificationController extends Controller
{

    public function __construct() {
        $this->middleware('auth', ['except' => ['verifyEmail']]);
    }


    public function index() {
        return view('email_verification.index', [
            'emails' => Auth::user()->emails()->where('verified', false)->get(),
            'authorization_url' => PlatformRequest::getRedirectUrl(),
        ]);
    }


    public function sendVerificationLink(Request $request) {
        if($email = Auth::user()->emails()->where('email', $request->input('email'))->where('verified', false)->first()) {
            $token = Verificator::getToken($email);
            $email->sendEmailVerificationNotification($token);
            return back()->with('status', trans('email_verification.sent', ['email' => $email->email]));
        }
        return redirect()->back();
    }


    public function verifyEmail($token) {
        $result = Verificator::verifyEmail($token);
        if($result == Verificator::EMAIL_VERIFIED) {
            return view('email_verification.result');
        }
        return view('email_verification.result', [ 'error' => $result ]);

    }

}
