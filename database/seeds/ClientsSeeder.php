<?php

use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{

    protected $defaults = [
        'user_id' => 0,
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false,
        'name' => 'Dev test platform',
        'secret' => '1AtKfSc7KbgIo8GDCI31pA9laP7pFoBqSg3RtVHq',
        'profile_fields' => [
            'login',
            'primary_email',
            'secondary_email'
            'first_name',
            'last_name',
            'language',
            'country_code',
            'address',
            'city',
            'zipcode',
            'primary_phone',
            'secondary_phone',
            'role',
            'birthday',
            'presentation',
        ],
        'auth_order' => [
            'login',
            'google'
        ],
    ];



    public function run()
    {
        \App\Client::create(array_merge($this->defaults, [
            'id' => 1,
            'redirect' => 'http://login-module-example-client.dev/callback_oauth.php'
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 2,
            'redirect' => 'http://login-module-example-client.mobydimk.space/callback_oauth.php'
        ]));
    }

}
