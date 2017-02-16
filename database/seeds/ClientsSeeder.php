<?php

use Illuminate\Database\Seeder;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $profile_fields = [
            'login',
            'language',
            'first_name',
            'last_name',
            'country_code',
            'address',
            'city',
            'zipcode',
            'primary_phone',
            'secondary_phone',
            'role',
            'birthday',
            'presentation',
            'primary_email',
            'secondary_email'
        ];

        \App\Client::create([
            'id' => 1,
            'user_id' => 0,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'name' => 'Dev test platform',
            'secret' => '1AtKfSc7KbgIo8GDCI31pA9laP7pFoBqSg3RtVHq',
            'profile_fields' => $profile_fields,
            'redirect' => 'http://login-module-example-client.dev/callback_oauth.php'
        ]);

        \App\Client::create([
            'id' => 2,
            'user_id' => 0,
            'personal_access_client' => false,
            'password_client' => false,
            'revoked' => false,
            'name' => 'Dev test platform',
            'secret' => '1AtKfSc7KbgIo8GDCI31pA9laP7pFoBqSg3RtVHq',
            'profile_fields' => $profile_fields,
            'redirect' => 'http://login-module-example-client.mobydimk.space/callback_oauth.php'
        ]);
    }

}
