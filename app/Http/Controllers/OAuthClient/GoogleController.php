<?php

namespace App\Http\Controllers\OAuthClient;

use Request;
use App\Traits\OAuthUserConnector;
use Arr;
use App\Traits\OAuthFrontendClient;

class GoogleController extends \App\Http\Controllers\Controller
{

    use OAuthFrontendClient, OAuthUserConnector;

    private function getClient() {
        $client = new \Google_Client();
        $client->setClientId(config('oauth_client.google.client_id'));
        $client->setClientSecret(config('oauth_client.google.client_secret'));
        $client->setScopes(['openid', 'profile', 'email']);
        $client->setRedirectUri(route('oauth_client_callback_google'));
        return $client;
    }


    public function redirect(Request $request) {
        $client = $this->getClient();
        $client->setState(json_encode($request->all()));
        $google_link = $client->createAuthUrl();
        return redirect($google_link);
    }


    public function callback(Request $request) {
        $callback_params = json_decode($request->get('state'), true);
        $client = $this->getClient();
        $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
        if(isset($token['error'])) {
            return $this->getFrontendRedirect($callback_params);
        }
        $client->setAccessToken($token);
        $token_data = $client->verifyIdToken();

        $user_data = [
            'provider' => 'google',
            'uid' => Arr::get($token_data, 'sub'),
            'email' => Arr::get($token_data, 'email'),
            'name' => Arr::get($token_data, 'given_name').' '.Arr::get($token_data, 'family_name')
        ];
        return $this->oauthConnect($callback_params, $user_data);
    }

}


