<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;
use App\User;
use App\Email;

class PeerValidationController extends Controller
{
    public function index(Request $request) {
        return view('verification.methods.peer');
    }


    public function store(Request $request) {
        $this->validate($request, [
            'email' => 'required'
        ]);

        $code = str_random(10);
        $v = $request->get('email');
        $user = null;
        if(strpos($v, '@') === false) {
            $user = User::where('login', $v)->first();
            if(count($user->emails) == 0) {
                return $this->userNotFoundResponse();
            }
            $user->emails[0]->peerVerificationRequest($code);
        } else if($email = Email::where('email', $v)->first()) {
            $email->peerVerificationRequest($code);
        } else {
            return $this->userNotFoundResponse();
        }


        $method = VerificationMethod::where('name', 'peer')->firstOrFail();
        $verification = new Verification([
            'method_id' => $method->id,
            'user_attributes' => $method->user_attributes,
            'status' => 'pending',
            'code' => $code
        ]);
        $request->user()->verifications()->save($verification);
        return redirect('/verification');
    }


    private function userNotFoundResponse() {
        return redirect()->back()->withErrors([
            'email' => trans('verification.peer.user_not_found')
        ]);
    }


    public function code($id, Request $request) {
        $verification = $request->user()->verifications()->findOrFail($id);
        return view('verification.methods.peer_code', [
            'verification' => $verification
        ]);
    }


    public function storeCode($id, Request $request) {
        $verification = $request->user()->verifications()->findOrFail($id);
        if($verification->code != $request->get('code')) {
            return redirect()->back()->withErrors([
                'code' => trans('verification.peer.wrong_code')
            ]);
        }
        $verification->code = null;
        $verification->status = 'approved';
        $verification->save();
        return redirect('/verification');
    }
}
