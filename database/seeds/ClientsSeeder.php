<?php

use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{

    protected $defaults = [
        'user_id' => 0,
        'personal_access_client' => false,
        'password_client' => false,
        'revoked' => false,
        'secret' => '1AtKfSc7KbgIo8GDCI31pA9laP7pFoBqSg3RtVHq',
        'public_key' => '',
        'profile_fields' => [
            'login',
            'primary_email',
            'first_name',
            'last_name',
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
            'redirect' => 'http://login-module-example-client.dev/callback_oauth.php',
            'badge_url' => 'http://login-module-example-client.dev/dummy_badge.php',
            'name' => 'Example client - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 2,
            'redirect' => 'http://login-module-example-client.mobydimk.space/callback_oauth.php',
            'badge_url' => 'http://login-module-example-client.mobydimk.space/dummy_badge.php',
            'name' => 'Example client - demo',
        ]));


        $algorea_profile_fields = [
            'student_id',
            'login',
            'primary_email',
            'first_name',
            'last_name',
            'gender',
            'birthday',
            'graduation_year',
            'country_code',
            'address',
            'zipcode',
            'city',
            'primary_phone',
            'secondary_phone',
            'language',
            'presentation',
            'website',
        ];
        \App\Client::create(array_merge($this->defaults, [
            'id' => 3,
            'redirect' => 'http://algorea-platform.dev/login/callback_oauth.php',
            'badge_url' => 'http://login-module-example-client.dev/dummy_badge.php',
            'profile_fields' => $algorea_profile_fields,
            'name' => 'Algorea - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 4,
            'redirect' => 'http://algorea-platform.mobydimk.space/login/callback_oauth.php',
            'badge_url' => 'http://login-module-example-client.mobydimk.space/dummy_badge.php',
            'profile_fields' => $algorea_profile_fields,
            'name' => 'Algorea - demo',
        ]));


        $france_ioi_profile_fields = [
            'login',
            'primary_email',
            'first_name',
            'last_name',
            'gender',
            'birthday',
            'graduation_year',
            'country_code',
            'address',
            'zipcode',
            'city',
            'primary_phone',
            'secondary_phone',
            'language',
            'presentation',
            'website',
            'student_id',
            'timezone'
        ];
        \App\Client::create(array_merge($this->defaults, [
            'id' => 5,
            'redirect' => 'http://france-ioi.dev/user/callback_oauth.php',
            'badge_url' => '',
            'profile_fields' => $france_ioi_profile_fields,
            'name' => 'franceioi - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 6,
            'redirect' => 'http://france-ioi.mobydimk.space/user/callback_oauth.php',
            'badge_url' => '',
            'profile_fields' => $france_ioi_profile_fields,
            'name' => 'franceioi - demo',
        ]));
    }

}
