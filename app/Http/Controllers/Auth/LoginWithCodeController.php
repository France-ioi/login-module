<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\AccountsManager;
use App\LoginModule\Platform\PlatformContext;
use App\Badge;
use App\PlatformGroup;
use Auth;

class LoginWithCodeController extends Controller
{


    public function __construct(PlatformContext $context,
                                AccountsManager $accounts_manager) {
        $this->middleware('guest');
        $this->context = $context;
        $this->accounts_manager = $accounts_manager;
    }


    // attempt auth by participation code
    public function login(Request $request) {
        $this->validate($request, [
            'identity' => 'required|string'
        ]);
        $code = $request->input('identity');

        // attempt login
        if($badge = $this->findBadge($code)) {
            if($badge->login_enabled) {
                Auth::login($badge->user, $request->has('remember'));
                return redirect($this->context->continueUrl('/account'));
            } else {
                return $this->sendLoginPasswordResponse($request);
            }
        }

        // attempt to use badge api
        if($badge_data = $this->context->badge()->verifyAndStoreData($code)) {
            if($badge_data['user']['id'] && ($badge_user = \App\User::where('id', $badge_data['user']['id'])->first())) {
                return $this->sendFailedCodeResponse($request);
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
        return $this->sendLoginPasswordResponse($request);
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



    private function findBadge($code) {
        return Badge::where('code', $code)->where(function($q) use ($code) {
            $q->where('code', '')->orWhere('code', $code);
        })->first();
    }



    public function sendFailedCodeResponse(Request $request)
    {
        $errors = ['identity' => trans('auth.failed')];
        return redirect()->back()
            ->withInput($request->only('identity', 'remember'))
            ->withErrors($errors);
    }


    public function sendLoginPasswordResponse(Request $request)
    {
        $data = [
            'remember' => $request->get('remember'),
            'login' => $request->get('identity')
        ];
        return redirect('/login')->withInput($data);
    }
}