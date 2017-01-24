<?php

namespace App\Traits;

use Illuminate\Support\Facades\Hash;
use App\User;
use App\OAuthConnection;

trait OAuthUserConnector
{

    private function getFrontendRedirect($callback_params, $token = false) {
        $url = '/authorization?'
            .http_build_query($callback_params)
            .($token ? '#token='.$token : '');
        return redirect($url);
    }

    private function oauthConnect($callback_params, $user_data) {
        $user = $this->getOAuthConnectionUser($user_data);
        $token_data = $this->issueAccessToken($user);
        return $this->getFrontendRedirect($callback_params, $token_data ? $token_data['access_token'] : false);
    }


    private function getOAuthConnectionUser($user_data) {
        if($connection = OAuthConnection::where('uid', $user_data['uid'])->where('provider', $user_data['provider'])->with('user')->first()) {
            return $connection->user;
        }

        if(empty($user_data['email'])) {
            $user_data['email'] = $user_data['provider'].$user_data['uid'].'@'.$_SERVER['SERVER_NAME']; // facebook user email may be empty
        }
        $user = User::where('email', $user_data['email'])->first();
        if(!$user) {
            $user_data['password'] = Hash::make(str_random());
            $user = User::create($user_data);
            $connection = new OAuthConnection($user_data);
            $user->oauth_connections()->save($connection);
        }
        return $user;
    }

}