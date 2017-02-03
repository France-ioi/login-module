<?php

namespace App\Http\Controllers\OAuthClient;

use Request;
use App\Traits\AuthConnector;
use Arr;
use App\Traits\OAuthFrontendClient;

class GoogleController extends \App\Http\Controllers\Controller
{

    use OAuthFrontendClient, AuthConnector;


    private function getProvider() {
        $provider = new \Google_Client();
        $provider->setClientId(config('oauth_client.google.client_id'));
        $provider->setClientSecret(config('oauth_client.google.client_secret'));
        $provider->setScopes(['openid', 'profile', 'email']);
        $provider->setRedirectUri(route('oauth_client_callback_google'));
        return $provider;
    }


    public function redirect(Request $request) {
        $provider = $this->getProvider();
        $provider->setState(json_encode($request->all()));
        $google_link = $provider->createAuthUrl();
        return redirect($google_link);
    }


    public function callback(Request $request) {
        $callback_params = json_decode($request->get('state'), true);
        $provider = $this->getProvider();
        $token = $provider->fetchAccessTokenWithAuthCode($request->get('code'));
        if(isset($token['error'])) {
            return $this->getFrontendRedirect($callback_params);
        }
        $provider->setAccessToken($token);
        $token_data = $provider->verifyIdToken();

        $user_data = [
            'provider' => 'google',
            'uid' => Arr::get($token_data, 'sub'),
            'email' => Arr::get($token_data, 'email'),
            'first_name' => Arr::get($token_data, 'given_name'),
            'last_name' => Arr::get($token_data, 'family_name')
        ];
        $user = $this->authConnect($user_data);
        return $this->getFrontendRedirect($callback_params, $user);
    }

}