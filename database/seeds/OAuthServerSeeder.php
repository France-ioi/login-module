<?php

use Illuminate\Database\Seeder;

class OAuthServerSeeder extends Seeder
{

    public function run()
    {
        $client = \App\OAuthClient::create([
            'id' => str_random(40),
            'secret' => str_random(40),
            'name' => 'frontend'
        ]);

        $client = \App\OAuthClient::create([
            'id' => str_random(40),
            'secret' => str_random(40),
            'name' => 'Login module test client dev'
        ]);
        $client->oauth_client_endpoint()->save(
            new \App\OAuthClientEndpoint(['redirect_uri' => 'http://login-module-client.dev/oauth_callback.php'])
        );

        $client = \App\OAuthClient::create([
            'id' => str_random(40),
            'secret' => str_random(40),
            'name' => 'Login module test client dev demo'
        ]);
        $client->oauth_client_endpoint()->save(
            new \App\OAuthClientEndpoint(['redirect_uri' => 'http://login-module-client.mobydimk.space/oauth_callback.php'])
        );
    }
}
