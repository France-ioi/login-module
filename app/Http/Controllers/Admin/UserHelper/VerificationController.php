<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserHelperAction;
use App\VerificationMethod;
use App\Verification;

class VerificationController extends Controller
{

    public function index($id, Request $request) {
        $user = User::findOrFail($id);
        if(!$request->user()->userHelperClients->pluck('id')->search($user->creator_client_id)) {
            abort(403);
        }
        $user_helper = $request->user()->userHelper;
        $verification = $user->verifications()->whereHas('method', function($q) {
            return $q->where('name', 'user_helper');
        })->first();
        return view('admin.user_helper.verification', [
            'user' => $user,
            'user_helper' => $user_helper,
            'verified_attributes' => $verification ? $verification->user_attributes : []
        ]);
    }


    public function verify($id, Request $request) {
        $user = User::findOrFail($id);
        if(!$request->user()->userHelperClients->pluck('id')->search($user->creator_client_id)) {
            abort(403);
        }

        $method = VerificationMethod::where('name', 'user_helper')->firstOrFail();
        $user->verifications()->where('method_id', $method->id)->delete();
        $verified_attributes = $this->getVerifiedAttributes($request);
        $user->verifications()->save(new Verification([
            'method_id' => $method->id,
            'user_attributes' => $verified_attributes,
            'status' => 'approved'
        ]));

        $request->user()->userHelperActions()->save(new UserHelperAction([
            'target_user_id' => $id,
            'type' => 'verification',
            'hash' => md5($id.json_encode($verified_attributes)),
            'details' => $verified_attributes
        ]));
        return redirect('/admin/user_helper')->with('status', 'Verification updated');
    }


    public function getVerifiedAttributes($request) {
        $verifiable_attributes = $request->user()->userHelper->verifiable_attributes;
        $verified = $request->get('verified');
        $res = [];
        foreach($verifiable_attributes as $attr) {
            if(in_array($attr, $verified)) {
                $res[] = $attr;
            }
        }
        return $res;
    }

}