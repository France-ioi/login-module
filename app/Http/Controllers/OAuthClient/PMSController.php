<?php

namespace App\Http\Controllers\OAuthClient;

use Request;
use App\Traits\OAuthConnector;
use App\Traits\OAuthFrontendClient;
use Arr;

class PMSController extends \App\Http\Controllers\Controller
{

    use OAuthFrontendClient, OAuthConnector;


    private function getProvider() {
        return new \League\OAuth2\Client\Provider\GenericProvider([
            'clientId' => config('oauth_client.pms.client_id'),
            'clientSecret' => config('oauth_client.pms.client_secret'),
            'redirectUri' => route('oauth_client_callback_pms'),
            'urlAuthorize' => 'https://pms-dev.bwinf.de/app/PMS/wa/OAuth2/authorize',
            'urlAccessToken' => 'https://pms-dev.bwinf.de/wa/OAuth2/token',
            'urlResourceOwnerDetails' => 'https://pms-dev.bwinf.de/wa/OAuth2/studentData',
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
            $user_data = [
                'provider' => 'pms',
                'uid' => Arr::get($owner, 'eMail'), //TODO need specs
                'email' => Arr::get($owner, 'eMail'),
                'name' => Arr::get($token_data, 'firstName').' '.Arr::get($token_data, 'lastName'),
                'first_name' => Arr::get($owner, 'firstName'),
                'last_name' => Arr::get($owner, 'lastName'),
                'gender' => Arr::get($owner, 'gender'),
                'birthday' => Arr::get($owner, 'dateOfBirth'),
                'school_class' => Arr::get($owner, 'schoolClass'),
                'school_id' => Arr::get($owner, 'schoolId'),
                'street1' => Arr::get($owner, 'street1'),
                'street2' => Arr::get($owner, 'street2'),
                'zip' => Arr::get($owner, 'zip'),
                'city' => Arr::get($owner, 'city'),
                
            ];
            return $this->oauthConnect($callback_params, $user_data);
        } catch (\League\OAuth2\Client\Provider\Exception\IdentityProviderException $e) {
            return $this->getFrontendRedirect($callback_params);
        }
    }

}