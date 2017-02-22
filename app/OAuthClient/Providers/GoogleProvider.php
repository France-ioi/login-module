<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;

    class GoogleProvider implements ProviderInterface {


        private $state_session_key = 'google_oauth_state';


        private function getClient() {
            $client = new \Google_Client([
                'openid.realm' => config('oauth_client.google.openid.realm')
            ]);
            $client->setClientId(config('oauth_client.google.client_id'));
            $client->setClientSecret(config('oauth_client.google.client_secret'));
            $client->setScopes(['openid', 'profile', 'email']);
            $calback_url = route('oauth_client_callback', ['provider' => 'google']);
            $client->setRedirectUri($calback_url);
            return $client;
        }


        public function getAuthorizationURL() {
            $state = str_random(40);
            session()->put($this->state_session_key, $state);
            $client = $this->getClient();
            $client->setState($state);
            return $client->createAuthUrl();
        }


        public function callback(\Illuminate\Http\Request $request) {
            if($request->get('state') !== session()->pull($this->state_session_key)) {
                return null;
            }
            $client = $this->getClient();
            $token = $client->fetchAccessTokenWithAuthCode($request->get('code'));
            if(isset($token['error'])) {
                return null;
            }
            $client->setAccessToken($token);
            $token_data = $client->verifyIdToken();
            return [
                'uid' => array_get($token_data, 'sub'),
                'uid_old' => array_get($token_data, 'openid_id'),
                'access_token' => $token['access_token'],
                'email' => array_get($token_data, 'email'),
                'first_name' => array_get($token_data, 'given_name'),
                'last_name' => array_get($token_data, 'family_name'),
                'picture' => array_get($token_data, 'picture'),
            ];
        }


        public function getLogoutURL(\App\User $user) {
            return null; //TODO
        }
    }