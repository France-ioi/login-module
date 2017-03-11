<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;

    class PMSProvider implements ProviderInterface {


        private $state_session_key = 'pms_oauth_state';


        private function getClient() {
            return new \League\OAuth2\Client\Provider\GenericProvider([
                'clientId' => config('oauth_client.pms.client_id'),
                'clientSecret' => config('oauth_client.pms.client_secret'),
                'redirectUri' => route('oauth_client_callback', ['provider' => 'pms']),
                'urlAuthorize' => 'https://pms-dev.bwinf.de/app/PMS/wa/OAuth2/authorize',
                'urlAccessToken' => 'https://pms-dev.bwinf.de/wa/OAuth2/token',
                'urlResourceOwnerDetails' => 'https://pms-dev.bwinf.de/wa/OAuth2/studentData',
                'scopes' => 'authenticate'
            ]);
        }


        public function getAuthorizationURL() {
            $state = str_random(40);
            session()->put($this->state_session_key, $state);
            $client = $this->getClient();
            return $client->getAuthorizationUrl([
                'state' => $state
            ]);
        }


        public function callback(\Illuminate\Http\Request $request) {
            if($request->get('state') !== session()->pull($this->state_session_key)) {
                return null;
            }
            $client = $this->getClient();
            try {
                $token = $client->getAccessToken('authorization_code', [ 'code' => $request->get('code') ]);
                $owner = $client->getResourceOwner($token)->toArray();
                return [
                    'uid' => array_get($owner, 'nickName'),
                    'access_token' => $token->getToken(),
                    'email' => array_get($owner, 'eMail'),
                    'first_name' => array_get($owner, 'firstName'),
                    'last_name' => array_get($owner, 'lastName'),
                    'school_id'=> array_get($owner, 'schoolId'),
                    'school_class' => array_get($owner, 'schoolClass'),
                ];
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return null;
            }
        }


        public function getLogoutURL($access_token, $redirect_url) {
            return null; //TODO
        }

    }