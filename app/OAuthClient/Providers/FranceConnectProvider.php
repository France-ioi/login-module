<?php

    /*
        dev version, based on specs:
        https://franceconnect.gouv.fr/fournisseur-service
    */

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use League\OAuth2\Client\Provider\GenericProvider;
    use Session;


    class FranceConnectProvider implements ProviderInterface {

        const STATE_SESSION_KEY = 'france_connect_oauth_state';

        const BASE_URL = 'https://fcp.integ01.dev-franceconnect.fr';

        private function getClient() {
            return new GenericProvider([
                'clientId' => config('oauth_client.france_connect.client_id'),
                'clientSecret' => config('oauth_client.france_connect.client_secret'),
                'redirectUri' => route('oauth_client_callback', ['provider' => 'france_connect']),
                'urlAuthorize' => self::BASE_URL.'/api/v1/authorize',
                'urlAccessToken' => self::BASE_URL.'/api/v1/token',
                'urlResourceOwnerDetails' => self::BASE_URL.'/api/v1/userinfo',
                'scopes' => ['openid','profile','email']
            ]);
        }


        public function getAuthorizationURL() {
            $state = str_random(40);
            session()->put(self::STATE_SESSION_KEY, $state);
            $client = $this->getClient();
            return $client->getAuthorizationUrl([
                'state' => $state
            ]);
        }


        public function getPreferencesURL($auth_connection) {
            return $this->getAuthorizationURL(); // TODO ?
        }


        public function callback(\Illuminate\Http\Request $request) {
            if($request->get('state') !== session()->pull(self::STATE_SESSION_KEY)) {
                return null;
            }
            try {
                $client = $this->getClient();
                $token = $client->getAccessToken('authorization_code', [ 'code' => $request->get('code') ]);
                $owner = $client->getResourceOwner($token)->toArray();
                return [
                    'uid' => array_get($owner, 'userID'),
                    'access_token' => $token->getToken(),
                    'birthday' => array_get($owner, 'birthdate'),
                    'first_name' => array_get($owner, 'given_name'),
                    'last_name' => array_get($owner, 'family_name'),
                    'language' => 'fr'
                ];
            } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
                return null;
            }
        }


        public function getLogoutURL($access_token, $redirect_url) {
            $p = [
                'post_logout_redirect_uri' => $redirect_url,
                'id_token_hint' => $access_token
            ];
            return self::BASE_URL.'/api/v1/logout?'.http_build_query($p);
        }


        public function getFixedFields() {
            return [];
        }

    }
