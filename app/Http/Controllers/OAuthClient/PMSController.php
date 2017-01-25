<?php

namespace App\Http\Controllers\OAuthClient;

use Request;
use App\Traits\OAuthUserConnector;
use App\Traits\OAuthFrontendClient;
use Arr;

class PMSController extends \App\Http\Controllers\Controller
{

    use OAuthFrontendClient, OAuthUserConnector;


    private function getProvider() {
        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => config('oauth_client.pms.client_id'),
            'clientSecret' => config('oauth_client.pms.client_secret'),
            'redirectUri' => route('oauth_client_callback_pms'),
            'urlAuthorize' => 'https://pms-dev.bwinf.de/app/PMS/wa/OAuth2/authorize', // TODO is it correct?
            'urlAccessToken' => '', // TODO find value
            'urlResourceOwnerDetails' => '', // TODO find value
            'scopes' => 'authenticate'
        ]);
    }


    public function redirect(Request $request) {
        $provider = $this->getProvider();
        $callback_params = json_encode($request->all());
        $authorization_url = $provider->getAuthorizationUrl([
            'state' => $callback_params
        ]);
        return redirect($authorization_url);
    }


    public function callback(Request $request) {
        $callback_params = json_decode($request->get('state'), true);
        $provider = $this->getProvider();
        try {
            $token = $provider->getAccessToken('authorization_code', [ 'code' => $request->get('code') ]);
            $owner = $provider->getResourceOwner($token)->toArray();

            $name = Arr::get($owner, 'nickName');
            $user_data = [
                'provider' => 'pms',
                'uid' => Arr::get($owner, 'eMail'), //TODO need specs
                'email' => Arr::get($token_data, 'eMail'),
                'name' => empty($name) ? Arr::get($token_data, 'firstName').' '.Arr::get($token_data, 'lastName') : $name
            ];
            return $this->oauthConnect($callback_params, $user_data);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return $this->getFrontendRedirect($callback_params);
        }
    }

}