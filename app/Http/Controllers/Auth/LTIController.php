<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Shared\TokenGenerator;
use App\LoginModule\Shared\TokenParser;
use App\LoginModule\AuthConnector;
use App\LoginModule\LoginGenerator;
use App\LoginModule\Keys;
use Validator;
use Auth;
use App\Client;

class LTIController extends Controller
{


    public function login(Request $request) {
        $validator = $this->getRequestValidator($request);
        if($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $platform = Client::where('name', $request->get('sPlatform'))->first();
        if(!$platform) {
            die('cannot find platform named '.$request->get('sPlatform'));
        }

        $parser = new TokenParser(
            $platform->public_key,
            $platform->name,
            'public'
        );
        $params = $parser->decodeJWS($request->get('sToken'));
        if(!isset($params['loginData'])) {
	        die('cannot find loginData array in token');
        }
        if($user = $this->getUser($params['loginData'], $platform)) {
            $token = $this->getUserToken($user);
            $url = $this->getRedirect($request, $token);
            return redirect($url);
        }
        return redirect('/auth');
    }


    private function getRequestValidator($request) {
        return Validator::make($request->all(), [
            'redirectUrl' => 'required',
            'sToken' => 'required',
            'sPlatform' => 'required'
        ]);
    }


    private function getUser($login_data, $platform) {
        if(!isset($login_data['lti_consumer_key']) ||
            !$login_data['lti_consumer_key'] ||
            !isset($login_data['lti_user_id']) ||
            !$login_data['lti_user_id']) {
			die('missing lti_consumer_key or lti_user_id in loginData');
		};

        $prefix = config('lti.default_login_prefix');;
        $prefixes_by_consumer = config('lti.prefixes_by_consumer');
        if(isset($prefixes_by_consumer[$login_data['lti_consumer_key']])) {
            $prefix = $prefixes_by_consumer[$login_data['lti_consumer_key']];
        }

        $login = LoginGenerator::genLogin(
            $login_data['firstName'],
            $login_data['lastName'],
            $prefix
        );

        $auth = [
            'uid' => $login_data['lti_consumer_key'].'::'.$login_data['lti_user_id'],
            'provider' => 'lti',
            'access_token' => 'none',
            'email' => $login_data['email'],
            'first_name' => $login_data['firstName'],
            'last_name' => $login_data['lastName'],
            'login' => $login
        ];
        \Auth::logout();
        return AuthConnector::connect($auth);
    }


    private function getUserToken($user) {
        $tokenParams = [
      	    'idUser' => $user['id'],
      	    'sLogin' => $user['login']
   	    ];
        $generator = new TokenGenerator(
            config('login_module.name'),
            Keys::getPrivate()
        );
	    return $generator->generateToken($tokenParams);
    }


    private function getRedirect($request, $token) {
        $url = $request->get('redirectUrl');
        return $url.(strpos($url, '?') === false ? '?' : '&').'loginToken='.$token;
    }

}
