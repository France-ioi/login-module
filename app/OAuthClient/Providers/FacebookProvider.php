<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use Facebook\PersistentData\PersistentDataInterface;


    class PersistentDataHandler implements PersistentDataInterface {

        protected $prefix = 'FBRLH_';

        public function get($key) {
            return session()->pull($this->prefix.$key);
        }

        public function set($key, $value) {
            session()->put($this->prefix.$key, $value);
        }
    }



    class FacebookProvider implements ProviderInterface {


        private function getClient() {
            return new \Facebook\Facebook([
                'app_id' => config('oauth_client.facebook.client_id'),
                'app_secret' => config('oauth_client.facebook.client_secret'),
                'default_graph_version' => 'v2.2',
                'persistent_data_handler' => new PersistentDataHandler()
            ]);
        }


        private function getToken($client, $code) {
            $helper = $client->getRedirectLoginHelper();
            try {
                $token = $helper->getAccessToken();
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                return false;
            } catch(Facebook\Exceptions\FacebookSDKException $e) {
                return false;
            }
            return $token;
        }


        private function getGraph($client, $token) {
            try {
                $response = $client->get('/me?fields=id,name,email', $token);
            } catch(\Facebook\Exceptions\FacebookResponseException $e) {
                return false;
            } catch(\Facebook\Exceptions\FacebookSDKException $e) {
                return false;
            }
            return $response->getGraphNode();
        }


        public function getAuthorizationURL() {
            $client = $this->getClient();
            $helper = $client->getRedirectLoginHelper();
            $calback_url = route('oauth_client_callback', ['provider' => 'facebook']);
            return $helper->getLoginUrl($calback_url, ['public_profile', 'email']);
        }


        public function getPreferencesURL() {
            return $this->getAuthorizationURL(); // TODO ?
        }


        public function callback(\Illuminate\Http\Request $request) {
            $client = $this->getClient();
            if($token = $this->getToken($client, $request->get('code'))) {
                if($graph = $this->getGraph($client, $token)) {
                    list($first_name, $last_name) = explode(' ', $graph->getField('name', ''), 2);
                    $id = $graph->getField('id');
                    return  [
                        'uid' => $id,
                        'access_token' => $token->getValue(),
                        'email' => $graph->getField('email', null),
                        'first_name' => $first_name,
                        'last_name' => $last_name,
                        'picture' => 'http://graph.facebook.com/'.$id.'/picture'
                    ];
                }
            }
            return null;
        }


        public function getLogoutURL($access_token, $redirect_url) {
            $client = $this->getClient();
            $helper = $client->getRedirectLoginHelper();
            return $helper->getLogoutUrl($access_token, $redirect_url);
        }

    }
