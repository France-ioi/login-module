<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Auth;
use Session;
use App\LoginModule\Platform\PlatformRequest;
use App\Badge;
use App\LoginModule\Platform\BadgeApi;

class BadgeController extends Controller
{

    private $validation_rules = [
        'code' => 'required'
    ];


    public function index() {
        return view('badge.index');
    }


    public function verify(Request $request) {
        $this->validate($request, $this->validation_rules);
        $code = $request->input('code');
        if($badge = $this->findBadge($code)) {
            return $this->failedVerificationResponse($code, trans('badge.code_registered'));
        }
        if(PlatformRequest::badge()->verifyAndStore($code)) {
            return redirect()->route('register');
        }
        return $this->failedVerificationResponse($code, trans('badge.code_verification_fail'));
    }


    public function attach(Request $request) {
        $this->validate($request, $this->validation_rules);
        $code = $request->input('code');
        if($badge = $this->findBadge($code)) {
            if($badge->user_id != Auth::user()->id) {
                return $this->failedVerificationResponse($code, trans('badge.code_registered'));
            }
        }
        if($badge_data = PlatformRequest::badge()->verify($request->input('code'))) {
            if($badge = Auth::user()->badges()->where('url', $badge_data['url'])->first()) {
                $badge->do_not_possess = false;
                $badge->code = $badge_data['code'];
                $badge->save();
                BadgeApi::update($badge->url, $badge->code, $badge->user_id);
            } else {
                Auth::user()->badges()->save(new Badge([
                    'code' => $badge_data['code'],
                    'url' => $badge_data['url']
                ]));
            }
            return redirect(PlatformRequest::getRedirectUrl('/badge'));
        }
        return $this->failedVerificationResponse($code, trans('badge.code_verification_fail'));
    }


    private function findBadge($code) {
        return Badge::where('url', PlatformRequest::badge()->url())->where('code', $code)->first();
    }


    private function failedVerificationResponse($code, $message) {
        $errors = new MessageBag([
            'code' => $message
        ]);
        return redirect()->back()->withInput(['code' => $code])->withErrors($errors);
    }


    public function doNotHave() {
        $url = PlatformRequest::badge()->url();
        if(!$badge = Auth::user()->badges()->where('url', $url)->first()) {
            Auth::user()->badges()->save(new Badge([
                'code' => '',
                'url' => $url,
                'do_not_possess' => true
            ]));
        }
        return redirect(PlatformRequest::getRedirectUrl('/badge'));
    }

}