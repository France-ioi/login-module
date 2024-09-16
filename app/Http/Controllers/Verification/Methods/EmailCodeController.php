<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class EmailCodeController extends Controller
{

    public function index(Request $request) {
        return view('verification.methods.email_code', [
            'emails' => $request->user()->emails->pluck('email', 'role')->toArray()
        ]);
    }


    public function store(Request $request) {
        if($request->get('resend') == '1') {
            return $this->resend($request);
        }
        $method = VerificationMethod::where('name', 'email_code')->firstOrFail();

        $email = $request->user()->emails()->where('role', $request->get('role'))->firstOrFail();
        if($email->code !== $request->get('code')) {
            return redirect()->back()->withErrors([
                'code' => trans('profile.email_verification_code_error')
            ]);
        }

        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => [$request->get('role').'_email'],
            'status' => 'approved'
        ]);
        $request->user()->verifications()->save($verification);
        return redirect('/verification');
    }

    private function resend(Request $request) {
        $email = $request->user()->emails()->where('role', $request->get('role'))->firstOrFail();

        $interval = config('verification.email_code_interval');
        if(!is_null($email->last_code_at) && $interval && Carbon::now()->timestamp - strtotime($email->last_code_at) < $interval) {
            return redirect()->back()->with('status', trans('verification.email_code.resend_wait'))->with('type', 'alert-danger');
        }

        $email->requireVerification();

        return redirect()->back()->with('status', trans('verification.email_code.resend_success', ['email' => $email->email]))->with('type', 'alert-success');
    }



}
