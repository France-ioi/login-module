<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Shared\TokenGenerator;
use App\LoginModule\Shared\TokenParser;
use App\Traits\AuthConnector;
use App\LoginModule\LoginGenerator;
use Validator;
use Auth;
use App\Client;

class LTIController extends Controller
{

    use AuthConnector;

    public function login(Request $request) {
        $validator = $this->getRequestValidator($request);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $platform = Client::where('name', $request->get('platform'))->first();
        if(!$platform) {
            die('cannot find platform named '.$request->get('platform'));
        }

        $parser = new TokenParser(
            $platform->public_key,
            $platform->name,
            'public'
        );
        $params = $parser->decodeJWS($request->get('token'));
        if(!isset($params['loginData'])) {
	        die('cannot find loginData array in token');
        }
        $user = $this->getUser($params['loginData'], $platform);
        Auth::login($user);
        $token = $this->getUserToken($user);
        $url = $this->getRedirect($request, $token);
        return redirect($url);
    }


    private function getRequestValidator($request) {
        return Validator::make($request->all(), [
            'redirectUrl' => 'required',
            'token' => 'required',
            'platform' => 'required'
        ]);
    }


    private function getUser($login_data, $platform) {
        if(!isset($login_data['lti_consumer_key']) ||
            !$login_data['lti_consumer_key'] ||
            !isset($login_data['lti_user_id']) ||
            !$login_data['lti_user_id']) {
			die('missing lti_consumer_key or lti_user_id in loginData');
		};

        $login = LoginGenerator::genLogin(
            $login_data['firstName'],
            $login_data['lastName'],
            'ups_'
        );

        $user_data = [
            'uid' => $login_data['lti_consumer_key'].'::'.$login_data['lti_user_id'],
            'provider' => 'lti',
            'email' => $login_data['email'],
            'name' => $login_data['first_name'].' '.$login_data['last_name'],
            'login' => $login
        ];
        return $this->authConnect($user_data);
    }


    private function getUserToken($user, $generator) {
        $tokenParams = [
      	    'idUser' => $user['id'],
      	    'sLogin' => $user['login'] //why?
   	    ];
        $generator = new TokenGenerator(
            config('login_module.name'),
            config('login_module.private_key')
        );
	    return $generator->generateToken($params);
    }


    private function getRedirect($request, $token) {
        $url = $request->get('redirectUrl');
        return $url.(strpos($url, '?') === false ? '?' : '&').'loginToken='.$token;
    }

}
