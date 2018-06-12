<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Auth;
use Session;
use App\Badge;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Platform\BadgeApi;
use App\LoginModule\UserDataGenerator;
use App\LoginModule\AccountsManager;
use App\User;
use App\Email;
use App\PlatformGroup;

class BadgeController extends Controller
{

    private $validation_rules = [
        'code' => 'required'
    ];


    public function __construct(PlatformContext $context, UserDataGenerator $generator, AccountsManager $accounts_manager) {
        $this->context = $context;
        $this->generator = $generator;
        $this->accounts_manager = $accounts_manager;
    }


    public function index() {
        return view('badge.index');
    }


    public function verify(Request $request) {
        $this->validate($request, $this->validation_rules);
        $code = $request->input('code');

        // attempt login
        if($badge = $this->findBadge($code)) {
            if($badge->login_enabled) {
                Auth::login($badge->user);
                return redirect($this->context->continueUrl('/account'));
            } else {
                return $this->failedVerificationResponse($code, trans('badge.code_registered'));
            }
        }

        // attempt to use badge api
        if($badge_data = $this->context->badge()->verifyAndStoreData($code)) {
            if($badge_data['user']['id'] && ($badge_user = \App\User::where('id', $badge_data['user']['id'])->first())) {
                return $this->failedVerificationResponse($code, trans('badge.code_in_use', ['username' => $badge_user->login]));
            }

            $client = $this->context->client();
            if($client && $client->badge_autologin) {
                return $this->authWithBadge($badge_data);
            }
            return redirect()->route('register');
        }

        // attempt to use participation code
        $client = $this->context->client();
        if($client && $this->context->platformApi()->verify($code)) {
            $data = $this->accounts_manager->create([
                'client_id' => $client->id,
                'prefix'=> 'user-',
                'postfix_length' => 8,
                'participation_code' => true,
                'language' => \App\LoginModule\Locale::get()
            ]);
            Auth::loginUsingId($data['id']);
            Auth::user()->platformGroups()->save(new PlatformGroup([
                'group_code' => $code,
                'client_id' => $client->id,
                'participation_code' => $data['participation_code']
            ]));
            return redirect('/participation_code');
        }
        return $this->failedVerificationResponse($code, trans('badge.code_verification_fail'));
    }


    private function authWithBadge($badge_data) {

        $user_data = $badge_data['user'];
        $email_used = $user_data['email'] && \App\Email::where('email', $user_data['email'])->first();
        $login_used = $user_data['login'] && \App\User::where('login', $user_data['login'])->first();
        if($email_used || $login_used) {
            return redirect()->route('register');
        }
        $this->context->badge()->flushData();

        if(!$user_data['email'] && !$user_data['login']) {
            $user_data['login'] = $this->generator->loginFromBadge($user_data, 'badge_');
        }
        $user = User::create($user_data);
        $user->badges()->save(new Badge([
            'code' => $badge_data['code'],
            'url' => $badge_data['url'],
            'data' => $badge_data['user']['data']
        ]));
        if($user_data['email']) {
            $user->emails()->save(new Email([
                'role' => 'primary',
                'email' => $user_data['email']
            ]));
        }
        BadgeApi::update($badge_data['url'], $badge_data['code'], $user->id);
        Auth::login($user);

        return redirect($this->context->continueUrl('/account'));
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
            if($badge = Auth::user()->badges()->where('url', $badge_data['url'])->first()) {
                $badge->do_not_possess = false;
                $badge->code = $badge_data['code'];
                $badge->data = $badge_data['user']['data'];
                $badge->save();
            } else {
                $badge = new Badge([
                    'code' => $badge_data['code'],
                    'url' => $badge_data['url'],
                    'data' => $badge_data['user']['data']
                ]);
                Auth::user()->badges()->save($badge);
            }
            BadgeApi::update($badge->url, $badge->code, $badge->user_id);
            $first_name_different = Auth::user()->first_name != $badge_data['user']['first_name'];
            $last_name_different = Auth::user()->last_name != $badge_data['user']['last_name'];
            if($first_name_different || $last_name_different) {
                return redirect('badge/confirm_difference')->with([
                    'badge_user' => $badge_data['user']
                ]);
            }
            return redirect($this->context->continueUrl('/badge'));
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
        $url = $this->context->badge()->url();
        if($badge = Auth::user()->badges()->where('url', $url)->first()) {
            $badge->comments = $request->get('comments');
            $badge->override_profile = $request->has('override_profile');
            $badge->save();
        }
        return redirect($this->context->continueUrl('/account'));
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
        $url = $this->context->badge()->url();
        if(!$badge = Auth::user()->badges()->where('url', $url)->first()) {
            Auth::user()->badges()->save(new Badge([
                'code' => null,
                'url' => $url,
                'do_not_possess' => true
            ]));
        }
        return redirect($this->context->continueUrl('/badge'));
    }

}
