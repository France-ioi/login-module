<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use League\OAuth2\Client\Provider\GenericProvider;

    class PMSClient extends GenericProvider {
        // Class to add the Authorization header required by PMS

        public function getHeaders($token = null)
        {
            if ($token) {
                return array_merge(
                    $this->getDefaultHeaders(),
                    $this->getAuthorizationHeaders($token)
                );
            }

            // Not sure why it should have this behavior
            $token = base64_encode(config('oauth_client.pms.client_id').':'.config('oauth_client.pms.client_secret'));
            return array_merge($this->getDefaultHeaders(), ['Authorization' => $token]);
        }
    }

    class PMSProvider implements ProviderInterface {
        // Interface between the app and the PMS provider

        private $state_session_key = 'pms_oauth_state';


        private function getClient($scopes='authenticate') {
            return new PMSClient([
                'clientId' => config('oauth_client.pms.client_id'),
                'clientSecret' => config('oauth_client.pms.client_secret'),
                'redirectUri' => route('oauth_client_callback', ['provider' => 'pms']),
                'urlAuthorize' => config('oauth_client.pms.base_url').'/wa/OAuth2/authorize',
                'urlAccessToken' => config('oauth_client.pms.base_url').'/wa/OAuth2/token',
                'urlResourceOwnerDetails' => config('oauth_client.pms.base_url').'/wa/OAuth2/studentData',
                'scopes' => $scopes
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


        public function getPreferencesURL() {
            $state = str_random(40);
            session()->put($this->state_session_key, $state);
            $client = $this->getClient('preferences');
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
                    'uid' => array_get($owner, 'nickName', array_get($owner, 'eMail')), // eMail if nickName is not present
                    'access_token' => $token->getToken(),
                    'email' => array_get($owner, 'eMail'),
                    'first_name' => array_get($owner, 'firstName'),
                    'last_name' => array_get($owner, 'lastName'),
                    'language' => 'de',
                    'pms_info' => $owner,
                ];
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return null;
            }
        }


        public function getLogoutURL($access_token, $redirect_url) {
            return null; //TODO
        }

    }
