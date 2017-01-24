<?php

namespace App\Http\Controllers\OAuthServer;

use Authorizer;

class AuthorizationController
{

    public function show() {
        $auth_params = Authorizer::getAuthCodeRequestParams();
        $auth_details= [
            'client' => [
                'id' => $auth_params['client']->getId(),
                'name' => $auth_params['client']->getName()
            ]
        ];
        return response()->json($auth_details);
    }


    public function authorize() {
        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = Authorizer::getResourceOwnerId();
        $redirect_uri = Authorizer::issueAuthCode('user', $params['user_id'], $params);
        $query = parse_url($redirect_uri,  PHP_URL_QUERY);
        parse_str($query, $query_params);
        return response()->json([
            'redirect_uri' => $redirect_uri,
            'result' => $query_params
        ]);
    }


    public function deny() {
        $params = Authorizer::getAuthCodeRequestParams();
        $params['user_id'] = Authorizer::getResourceOwnerId();
        $redirect_uri = Authorizer::authCodeRequestDeniedRedirectUri();
        $query = parse_url($redirect_uri,  PHP_URL_QUERY);
        parse_str($query, $query_params);
        return response()->json([
            'redirect_uri' => $redirect_uri,
            'result' => $query_params
        ]);
    }

}
