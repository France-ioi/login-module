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
        $content_id = $request->get('content_id');
        // Explicitly set the origin because LTI seems to end up with the wrong order of GET parameters and reject the request
        $origin = [
            'http_method' => 'POST',
            'http_url' => route('lti_launch') . '?content_id=' . $content_id . '&redirect_url=' . urlencode($url)
        ];
        $lc = $lti->handleRequest($origin);
        Auth::login($lc->user);
        $lc->content_id = $content_id;
        $lc->save();
        return $this->getRedirect($url, $lc->user->id, $lc->content_id);
    }


    private function getRedirect($url, $user_id, $content_id) {
        $url .= (strpos($url, '?') === false ? '?' : '&').'user_id='.urlencode($user_id);
        if($content_id !== null) {
            $url .= '&content_id='.urlencode($content_id);
        }
        return redirect($url, 303);
    }


    private function getRequestValidator($request) {
        return Validator::make($request->all(), [
            'redirect_url' => 'required'
        ]);
    }

}
