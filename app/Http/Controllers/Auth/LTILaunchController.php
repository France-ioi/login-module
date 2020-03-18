<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\LTI\LTIHelper;
use Auth;
use Validator;

class LTILaunchController extends Controller
{


    public function handle(Request $request, LTIHelper $lti) {
        $validator = $this->getRequestValidator($request);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $url = $request->get('redirect_url');
        $lti_user_id = $request->get('user_id');
        if($lti_user_id && Auth::check()) {
            $user = Auth::user();
            if($user->ltiConnections()->where('lti_user_id', $lti_user_id)->first()) {
                return $this->getRedirect($url, $user->id);
            }
        }
        $lc = $lti->handleRequest();
        Auth::login($lc->user);
        return $this->getRedirect($url, $lc->user->id, $lc->lti_content_id);
    }


    private function getRedirect($url, $user_id, $lti_content_id) {
        $url .= (strpos($url, '?') === false ? '?' : '&').
            'user_id='.urlencode($user_id).
            'content_id='.urlencode($lti_content_id);
        return redirect($url, 303);
    }


    private function getRequestValidator($request) {
        return Validator::make($request->all(), [
            'redirect_url' => 'required'
        ]);
    }

}
