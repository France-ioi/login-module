<?php
namespace Database\Seeders;

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
        'user_attributes' => [
            'login',
            'primary_email',
            'first_name',
            'last_name'
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
            'redirect' => 'http://login-module-example-client.test/callback_oauth.php',
            //'badge_url' => 'http://login-module-example-client.test/dummy_badge.php',
            'name' => 'Example client - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 2,
            'redirect' => 'http://login-module-example-client.mobydimk.space/callback_oauth.php',
            //'badge_url' => 'http://login-module-example-client.mobydimk.space/dummy_badge.php',
            'name' => 'Example client - demo',
        ]));


        $user_attributes = [
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
            'redirect' => 'http://algorea-platform.test/login/callback_oauth.php',
            //'badge_url' => 'http://login-module-example-client.test/dummy_badge.php',
            'user_attributes' => $user_attributes,
            'name' => 'Algorea - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 4,
            'redirect' => 'http://algorea-platform.mobydimk.space/login/callback_oauth.php',
            //'badge_url' => 'http://login-module-example-client.mobydimk.space/dummy_badge.php',
            'user_attributes' => $user_attributes,
            'name' => 'Algorea - demo',
        ]));


        $user_attributes = [
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
            'redirect' => 'http://france-ioi.test/user/callback_oauth.php',
            'user_attributes' => $user_attributes,
            'name' => 'franceioi - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 6,
            'redirect' => 'http://france-ioi.mobydimk.space/user/callback_oauth.php',
            'user_attributes' => $user_attributes,
            'name' => 'franceioi - demo',
        ]));


        $user_attributes = [
            'primary_email',
            'teacher_domain_verified',
            'first_name',
            'last_name',
            'gender',
            'country_code',
            'role',
            'presentation',
        ];
        \App\Client::create(array_merge($this->defaults, [
            'id' => 7,
            'redirect' => 'http://bebras-platform.test/teacherInterface/login_module/callback_oauth.php',
            'user_attributes' => $user_attributes,
            'name' => 'Bebras - dev',
        ]));
        \App\Client::create(array_merge($this->defaults, [
            'id' => 8,
            'redirect' => 'http://bebras.mobydimk.space/teacherInterface/login_module/callback_oauth.php',
            'user_attributes' => $user_attributes,
            'name' => 'Bebras - demo',
        ]));


    }

}
