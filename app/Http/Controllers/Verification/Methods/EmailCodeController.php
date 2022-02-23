<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class EmailCodeController extends Controller
{

    public function index(Request $request) {
        return view('verification.methods.email_code_step1', [
            'emails' => $request->user()->emails->pluck('email', 'role')->toArray()
        ]);
    }


    public function sendCode(Request $request) {
        $role = $request->get('role');
        $email = $request->user()->emails()->where('role', $role)->firstOrFail();
        $email->sendVerificationCode();
        return redirect('/verification/email_code/input_code/'.$role);
    }


    public function showInputCode($role, Request $request) {
        $email = $request->user()->emails()->where('role', $role)->firstOrFail();
        return view('verification.methods.email_code_step2', [
            'email' => $email,
            'code' => $request->get('code')
        ]);
    }


    public function validateCode($role, Request $request) {
        $method = VerificationMethod::where('name', 'email_code')->firstOrFail();

        $email = $request->user()->emails()->where('role', $role)->firstOrFail();
        if($email->code !== $request->get('code')) {
            return redirect()->back()->withErrors([
                'code' => trans('profile.email_verification_code_error')
            ]);
        }

        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => [$email->role.'_email'],
            'status' => 'approved',
            'email' => $email->email
        ]);
        $request->user()->verifications()->save($verification);
        return redirect('/verification')->with([
            'last_verification_attributes' => $verification->user_attributes
        ]);;
    }
}
