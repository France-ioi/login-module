<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Email;
use App\Badge;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\LoginModule\Platform\PlatformRequest;
use App\LoginModule\Locale;;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/account';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }


    public function showRegistrationForm()
    {
        $required = PlatformRequest::profileFields()->getRequired();
        $badge_data = PlatformRequest::badge()->restoreData();
        $values = $badge_data ? $badge_data['user'] : [];

        return view('auth.register', [
            'login_required' => array_search('login', $required) !== false,
            'email_required' => array_search('primary_email', $required) !== false,
            'values' => $values
        ]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        $required = PlatformRequest::profileFields()->getRequired();
        $validation_rules = PlatformRequest::profileFields()->getValidationRules($required);
        $reg_rules = [
            'password' => 'required|min:6|confirmed'
        ];
        if($validation_rules['login']) {
            $reg_rules['login'] = $validation_rules['login'];
        }
        if($validation_rules['primary_email']) {
            $reg_rules['primary_email'] = $validation_rules['primary_email'];
        }
        return Validator::make($data, $reg_rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'login' => $data['login'],
            'password' => md5($data['password']),
            'language' => Locale::get()
        ]);
        if(isset($data['primary_email'])) {
            $user->emails()->save(new Email([
                'role' => 'primary',
                'email' => $data['primary_email']
            ]));
        }
        if($badge_data = PlatformRequest::badge()->restoreData()) {
            $user->badges()->save(new Badge([
                'code' => $badge_data['code'],
                'url' => $badge_data['url']
            ]));
        }

        return $user;
    }

    public function registered($request, $user) {
        return redirect()->intended('/account');
    }

}
