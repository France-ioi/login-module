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
use App\LoginModule\Profile\SchemaBuilder;


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
    public function __construct(PlatformContext $context, SchemaBuilder $schema_builder) {
        $this->middleware('guest');
        $this->context = $context;
        $this->schema_builder = $schema_builder;
    }


    public function showRegistrationForm()
    {
        $required = $this->requiredAttributes();
        $badge_data = $this->context->badge()->restoreData();
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
        $required = $this->requiredAttributes();
        $schema = $this->schema_builder->build(null, $this->requiredAttributes(), []);
        $validation_rules = $schema->rules();
        $reg_rules = [
            'password' => 'required|min:6|confirmed'
        ];
        if(isset($validation_rules['login'])) {
            $reg_rules['login'] = $validation_rules['login'];
        }
        if(isset($validation_rules['primary_email'])) {
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
        return redirect()->intended('/account');
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
