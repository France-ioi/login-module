<?php

    namespace App\OAuthClient\Providers;

    use App\OAuthClient\Providers\ProviderInterface;
    use Facebook\PersistentData\PersistentDataInterface;
    use Facebook\Url\UrlDetectionInterface;


    class PersistentDataHandler implements PersistentDataInterface {

        const PREFIX = 'FB_PDH_';

        public function get($key) {
            return session()->pull(self::PREFIX.$key);
        }

        public function set($key, $value) {
            session()->put(self::PREFIX.$key, $value);
        }
    }


    class UrlDetectionHandler implements UrlDetectionInterface {

        public function getCurrentUrl() {
            return \Request::url();
        }

    }


    class FacebookProvider implements ProviderInterface {


        private function getClient() {
            return new \Facebook\Facebook([
                'app_id' => config('oauth_client.facebook.client_id'),
                'app_secret' => config('oauth_client.facebook.client_secret'),
                'default_graph_version' => 'v2.3',
                'persistent_data_handler' => new PersistentDataHandler(),
                'url_detection_handler' => new UrlDetectionHandler()
            ]);
        }


        private function getToken($client) {
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


        public function getPreferencesURL($auth_connection) {
            return $this->getAuthorizationURL(); // TODO ?
        }


        public function callback(\Illuminate\Http\Request $request) {
            $client = $this->getClient();
            if($token = $this->getToken($client)) {
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


        public function getFixedFields() {
            return [];
        }

    }
