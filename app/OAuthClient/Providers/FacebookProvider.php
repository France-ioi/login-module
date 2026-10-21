<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use League\OAuth2\Client\Provider\Facebook;
    use League\OAuth2\Client\Provider\Exception\IdentityProviderException;


    // Uses league/oauth2-facebook since the PHP 8 port (facebook/graph-sdk is
    // abandoned and does not support PHP 8)
    class FacebookProvider implements ProviderInterface {

        const STATE_SESSION_KEY = 'FB_PDH_state';


        private function getClient() {
            return new Facebook([
                'clientId' => config('oauth_client.facebook.client_id'),
                'clientSecret' => config('oauth_client.facebook.client_secret'),
                'redirectUri' => route('oauth_client_callback', ['provider' => 'facebook']),
                'graphApiVersion' => config('oauth_client.facebook.graph_api_version', 'v2.3'),
                'fields' => ['id', 'name', 'email']
            ]);
        }


        private function getToken($client, $request) {
            $state = session()->pull(self::STATE_SESSION_KEY);
            if(!$state || $request->get('state') !== $state || !$request->get('code')) {
                return false;
            }
            try {
                return $client->getAccessToken('authorization_code', [
                    'code' => $request->get('code')
                ]);
            } catch(IdentityProviderException $e) {
                return false;
            } catch(\UnexpectedValueException $e) {
                return false;
            }
        }


        private function getGraph($client, $token) {
            try {
                return $client->getResourceOwner($token)->toArray();
            } catch(IdentityProviderException $e) {
                return false;
            } catch(\UnexpectedValueException $e) {
                return false;
            }
        }


        public function getAuthorizationURL() {
            $client = $this->getClient();
            $url = $client->getAuthorizationUrl([
                'scope' => ['public_profile', 'email']
            ]);
            session()->put(self::STATE_SESSION_KEY, $client->getState());
            return $url;
        }


        public function getPreferencesURL($auth_connection) {
            return $this->getAuthorizationURL(); // TODO ?
        }


        public function callback(\Illuminate\Http\Request $request) {
            $client = $this->getClient();
            if($token = $this->getToken($client, $request)) {
                if($graph = $this->getGraph($client, $token)) {
                    $names = explode(' ', array_get($graph, 'name', ''), 2);
                    $id = array_get($graph, 'id');
                    return  [
                        'uid' => $id,
                        'access_token' => $token->getToken(),
                        'email' => array_get($graph, 'email'),
                        'first_name' => $names[0],
                        'last_name' => isset($names[1]) ? $names[1] : null,
                        'picture' => 'http://graph.facebook.com/'.$id.'/picture'
                    ];
                }
            }
            return null;
        }


        public function getLogoutURL($access_token, $redirect_url) {
            return 'https://www.facebook.com/logout.php?'.http_build_query([
                'next' => $redirect_url,
                'access_token' => $access_token
            ], '', '&');
        }


        public function getFixedFields() {
            return [];
        }

    }
