<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Email;
use App\Badge;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\LoginModule\Locale;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Profile\SchemaConfig;


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
    protected $redirectTo = '/profile';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(PlatformContext $context) {
        $this->middleware('guest');
        $this->context = $context;
    }


    public function showRegistrationForm()
    {
        $required = $this->requiredAttributes();
        $badge_data = $this->context->badge()->restoreData();
        $values = $badge_data ? $badge_data['user'] : [];
        $client = $this->context->client();

        return view('auth.register', [
            'login_required' => array_search('login', $required) !== false,
            'email_required' => array_search('primary_email', $required) !== false,
            'platform_name' => $client ? $client->name : trans('app.name'),
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
        $attributes = $this->requiredAttributes();
        $rules = [
            'password' => 'required|min:6|confirmed'
        ];
        foreach($attributes as $attr) {
            $config = SchemaConfig::$attr();
            $rule = [];
            if(isset($config['required']))  {
                $rule = (array) $config['required'];
            }
            if(isset($config['valid'])) {
                $rule = array_merge($rule, (array) $config['valid']);
            }
            $rules[$attr] = $rule;
        }
        return Validator::make($data, $rules);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        $required = $this->requiredAttributes();
        $user_data = [
            'password' => bcrypt($data['password']),
            'language' => Locale::get()
        ];
        if(array_search('login', $required) !== false) {
            $user_data['login'] = $data['login'];
        }
        $user = User::create($user_data);

        if(array_search('primary_email', $required) !== false) {
            $email = new Email([
                'role' => 'primary',
                'email' => $data['primary_email']
            ]);
            $user->emails()->save($email);
            $email->requireVerification();
        }
        if($badge_data = $this->context->badge()->restoreData()) {
            $user->badges()->save(new Badge([
                'code' => $badge_data['code'],
                'url' => '',
                'badge_api_id' => $badge_data['badge_api_id'],
                'data' => $badge_data['user']['data']
            ]));
        }

        return $user;
    }



    public function registered($request, $user) {
        return redirect()->intended('/profile');
    }


    private function requiredAttributes() {
        $attributes = ['login', 'primary_email'];
        if($client = $this->context->client()) {
            $attributes = array_intersect($attributes, $client->user_attributes);
        }
        if(!count($attributes)) {
            $attributes = ['login'];
        }
        return $attributes;
    }


}
