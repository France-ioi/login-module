<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use League\OAuth2\Client\Provider\GenericProvider;
    use Session;

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

        const STATE_SESSION_KEY = 'pms_oauth_state';

        const FIXED_FIELDS = [
            'primary_email',
            'birthday',
            'first_name',
            'last_name',
            'language'
        ];


        private function getClient($scopes = 'authenticate') {
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
            session()->put(self::STATE_SESSION_KEY, $state);
            $client = $this->getClient();
            return $client->getAuthorizationUrl([
                'state' => $state
            ]);
        }


        public function getPreferencesURL($auth_connection) {
            $state = str_random(40);
            session()->put(self::STATE_SESSION_KEY, $state);
            $client = $this->getClient('preferences');
            $url = $client->getAuthorizationUrl([
                'state' => $state
            ]);
            return $url . '&refresh_token=' . $auth_connection->refresh_token;
        }


        public function callback(\Illuminate\Http\Request $request) {
            if($request->get('state') !== session()->pull(self::STATE_SESSION_KEY)) {
                return null;
            }
            $scope = $request->get('scope');
            if(!$scope) {
                $scope = 'authenticate';
            }
            $client = $this->getClient($scope);
            try {
                $token = $client->getAccessToken('authorization_code', [ 'code' => $request->get('code') ]);
                $owner = $client->getResourceOwner($token)->toArray();
                return [
                    'uid' => array_get($owner, 'userID'),
                    'login' => array_get($owner, 'nickName', array_get($owner, 'eMail')), // eMail if nickName is not present
                    'access_token' => $token->getToken(),
                    'refresh_token' => $token->getRefreshToken(),
                    'email' => array_get($owner, 'eMail'),
                    'birthday' => array_get($owner, 'dateOfBirth'),
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


        public function getFixedFields() {
            return self::FIXED_FIELDS;
        }

    }
