<?php

namespace App\Traits;

use App\User;
use App\OAuthConnection;

trait OAuthFrontendClient
{

    private function getFrontendRedirect($callback_params, $user = false) {
        $url = route('frontend').'?'.http_build_query($callback_params);
        if($user) {
            $token = $this->issueAccessToken($user);
            $url .= $token ? '#token='.$token['access_token'] : '';
        }
        return redirect($url);
    }


    private function issueAccessToken($user) {
        $client = \App\OAuthClient::where('name', 'frontend')->first();
        if(!$client) {
            return null;
        }
        $params = [
            'username' => $user->email,
            'password' => $user->password,
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'grant_type' => 'password'
        ];
        $http_client = new \GuzzleHttp\Client([
            'http_errors' => false
        ]);
        $response = $http_client->request('POST', route('access_token'), [ 'form_params' => $params ]);
        if($response->getStatusCode() == 200) {
            $res = json_decode($response->getBody()->getContents(), true);
            if($res && isset($res['access_token'])) {
                return $res;
            }
        }
        return null;
    }

}