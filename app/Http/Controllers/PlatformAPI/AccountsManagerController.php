<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AutoLoginToken;
use App\LoginModule\UserDataGenerator;

class AccountsManagerController extends Controller
{

    protected $generator;


    public function __construct(UserDataGenerator $generator) {
        $this->generator = $generator;
    }



    public function create(Request $request) {
        $res = [];
        for($i=0; $i<$request->get('amount'); $i++) {
            $password = $this->generator->password();
            $login = $this->generator->login($request->get('prefix'));
            $login_fixed = !$request->get('auto_login'); //TODO: add option for $login_fixed

            $user = new User([
                'login' => $login,
                'password' => \Hash::make($password)
            ]);
            $user->login_fixed = $login_fixed;
            $user->creator_client_id = $request->get('client_id');
            $user->save();

            $token = '';
            if($request->get('auto_login')) {
                $token = $this->generator->autoLoginToken();
                $user->autoLoginToken()->save(new AutoLoginToken([
                    'token' => $token
                ]));
            }

            $res[] = [
                'id' => $user->id,
                'login' => $login,
                'password' => $password,
                'auto_login_token' => $token
            ];
        }
        return $this->makeResponse($res, $request->get('secret'));
    }


    public function delete(Request $request) {
        if(!empty($request->get('prefix'))) {
            $prefix = str_replace('_', '\_', $request->get('prefix')).'%';
            User::where('login', 'like', $prefix)->where('creator_client_id', $request->get('client_id'))->delete();
        }
        return $this->makeResponse(true, $request->get('secret'));
    }


    private function makeResponse($res, $secret) {
        $res = json_encode($res);
        $res = openssl_encrypt($res, 'AES-128-ECB', $secret);
        return response($res);
    }

}