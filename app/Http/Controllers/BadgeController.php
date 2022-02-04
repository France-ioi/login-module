<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Auth;
use Session;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Platform\BadgeRequest;
use App\LoginModule\UserDataGenerator;
use App\User;
use App\Email;
use App\Badge;

class BadgeController extends Controller
{

    private $validation_rules = [
        'code' => 'required'
    ];


    public function __construct(PlatformContext $context,
                                UserDataGenerator $generator) {
        $this->context = $context;
        $this->generator = $generator;
    }


    public function index() {
        return view('badge.index');
    }



    public function attach(Request $request) {
        $this->validate($request, $this->validation_rules);
        $code = $request->input('code');
        if($badge = $this->findBadge($code)) {
            if($badge->user_id != Auth::user()->id) {
                return $this->failedVerificationResponse($code, trans('badge.code_registered'));
            }
        }
        if($badge_data = $this->context->badge()->verify($request->input('code'))) {
            if($badge_data['user']['id'] && $badge_data['user']['id'] != Auth::user()->id
                    && ($badge_user = \App\User::where('id', $badge_data['user']['id'])->first())) {
                return $this->failedVerificationResponse($code, trans('badge.code_in_use', ['username' => $badge_user->login]));
            }
            if($badge = Auth::user()->badges()->where('badge_api_id', $badge_data['badge_api_id'])->first()) {
                $badge->do_not_possess = false;
                $badge->code = $badge_data['code'];
                $badge->data = $badge_data['user']['data'];
                $badge->save();
            } else {
                $badge = new Badge([
                    'code' => $badge_data['code'],
                    'url' => '',
                    'badge_api_id' => $badge_data['badge_api_id'],
                    'data' => $badge_data['user']['data']
                ]);
                Auth::user()->badges()->save($badge);
            }
            if($badge->badge_api_id) {
                BadgeRequest::update($badge->badgeApi->url, $badge->code, $badge->user_id);
            }

            $first_name_different = Auth::user()->first_name != $badge_data['user']['first_name'];
            $last_name_different = Auth::user()->last_name != $badge_data['user']['last_name'];
            if($first_name_different || $last_name_different) {
                return redirect('badge/confirm_difference')->with([
                    'badge_user' => $badge_data['user']
                ]);
            }
            return redirect('/redirect/continue?alternative_url='.urlencode('/badge'));
        }
        return $this->failedVerificationResponse($code, trans('badge.code_verification_fail'));
    }


    public function getConfirmDifference(Request $request) {
        return view('badge.confirm_difference', [
            'badge_user' => session('badge_user'),
            'user' => Auth::user()
        ]);
    }


    public function confirmDifference(Request $request) {
        $api = $this->context->badge()->api();
        if($api && ($badge = Auth::user()->badges()->where('badge_api_id', $api->id)->first())) {
            $badge->comments = $request->get('comments');
            $badge->override_profile = $request->filled('override_profile');
            $badge->save();
        }
        return redirect('/redirect/continue');
    }


    private function findBadge($code) {
        return Badge::where('code', $code)->where(function($q) use ($code) {
            $q->where('code', '')->orWhere('code', $code);
        })->first();
    }


    private function failedVerificationResponse($code, $message) {
        $errors = new MessageBag([
            'code' => $message
        ]);
        return redirect()->back()->withInput(['code' => $code])->withErrors($errors);
    }


    public function doNotHave() {
        $api = $this->context->badge()->api();
        if($api && !($badge = Auth::user()->badges()->where('badge_api_id', $api->id)->first())) {
            Auth::user()->badges()->save(new Badge([
                'code' => null,
                'url' => '',
                'badge_api_id' => $api->id,
                'do_not_possess' => true
            ]));
        }
        return redirect('/redirect/continue?alternative_url='.urlencode('/badge'));
    }

}
