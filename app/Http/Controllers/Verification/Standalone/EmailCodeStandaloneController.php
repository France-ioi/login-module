<?php

namespace App\Http\Controllers\Verification\Standalone;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;
use App\Email;

class EmailCodeStandaloneController extends Controller
{

    public function index(Request $request) {
        return view('verification.standalone.email_code', $this->verify($request));
    }

    private function verify(Request $request) {
        $address = $request->get('email');
        $code = $request->get('code');
        if(!$address || !$code) {
            return [
                'type' => 'alert-danger',
                'message' => trans('profile.email_verification_request_error')
            ];
        }
        $email = Email::where('email', $address)->where('code', $code)->first();
        if(!$email) {
            return [
                'type' => 'alert-danger',
                'message' => trans('profile.email_verification_code_error')
            ];
        }

        $method = VerificationMethod::where('name', 'email_code')->firstOrFail();
        $verification = new Verification([
            'method_id' => $method->id,
            'user_id' => $email->user_id,
            'user_attributes' => [$email->role.'_email'],
            'status' => 'approved'
        ]);
        $verification->save();

        return [
            'type' => 'alert-success',
            'message' => trans('profile.email_verification_success')
        ];
    }
}
