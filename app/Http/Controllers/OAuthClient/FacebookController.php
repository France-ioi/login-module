<?php

namespace App\Http\Controllers\OAuthClient;

use Request;
use App\Traits\OAuthUserConnector;
use App\Traits\OAuthFrontendClient;

class FacebookController extends \App\Http\Controllers\Controller
{

    use OAuthFrontendClient, OAuthUserConnector;

    public function __construct()
    {
        session_start(); // dirty hack for fb api
    }


    private function getProvider() {
        return new \Facebook\Facebook([
            'app_id' => config('oauth_client.facebook.client_id'),
            'app_secret' => config('oauth_client.facebook.client_secret'),
            'default_graph_version' => 'v2.2',
        ]);
    }


    public function redirect(Request $request) {
        $provider = $this->getProvider();
        $helper = $provider->getRedirectLoginHelper();
        $callback_url = route('oauth_client_callback_facebook').'?callback_params='.urlencode(json_encode($request->all()));
        $url = $helper->getLoginUrl($callback_url, ['public_profile', 'email']);
        return redirect($url);
    }


    public function callback(Request $request) {
        $callback_params = json_decode($request->get('callback_params'), true);
        $provider = $this->getProvider();
        if($token = $this->getToken($provider, $request->get('code'))) {
            if($graph = $this->getGraph($provider, $token)) {
                $user_data = [
                    'provider' => 'facebook',
                    'uid' => $graph->getField('id'),
                    'email' => $graph->getField('email', ''),
                    'name' => $graph->getField('name', '')
                ];
                return $this->oauthConnect($callback_params, $user_data);
            }
        }
        return $this->getFrontendRedirect($callback_params);
    }



    private function getToken($provider, $code) {
        $helper = $provider->getRedirectLoginHelper();
        try {
            $token = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return false;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }
        return $token;
    }


    private function getGraph($provider, $token) {
        try {
            $response = $provider->get('/me?fields=id,name,email', $token);
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            return false;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            return false;
        }
        return $response->getGraphNode();
    }

}
