<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\LTI\LTIHelper;
use Auth;
use Validator;
use Cookie;

class LTILaunchController extends Controller
{

    public function __construct() {
        $this->middleware('guest');
    }


    public function handle(Request $request, LTIHelper $lti) {
        $validator = $this->getRequestValidator($request);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $url = $request->get('redirect_url');
        $user_id = $request->cookie('user_id');
        if($user_id && Auth::check() && Auth::user()->id == $user_id) {
            return $this->getRedirect($url, $user_id);
        }
        $lc = $lti->handleRequest();
        Auth::login($lc->user);
        return $this->getRedirect($url, $lc->user->id);
    }


    private function getRedirect($url, $user_id) {
        $url .= (strpos($url, '?') === false ? '?' : '&').'login_id='.urlencode($user_id);
        $cookie = Cookie::make('user_id', $user_id, 86400);
        return redirect($url, 303)->withCookie($cookie);
    }


    private function getRequestValidator($request) {
        return Validator::make($request->all(), [
            'redirect_url' => 'required'
        ]);
    }

}
