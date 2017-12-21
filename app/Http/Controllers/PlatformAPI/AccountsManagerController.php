<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\AutoLoginToken;

class AccountsManagerController extends Controller
{

    public function create(Request $request) {
        $res = [];
        for($i=0; $i<$request->get('amount'); $i++) {
            $password = $this->randomStr();
            $login = $this->generateLogin($request->get('prefix'));

            $user = new User([
                'login' => $login,
                'password' => \Hash::make($password)
            ]);
            $user->login_fixed = true;
            $user->creator_client_id = $request->get('client_id');
            $user->save();

            $token = '';
            if($request->get('auto_login')) {
                $token = $this->randomStr(50);
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
            $prefix = str_replace('_', '\_', $request->get('prefix')).'\_%';
            User::where('login', 'like', $prefix)->where('creator_client_id', $request->get('client_id'))->delete();
        }
        return $this->makeResponse(true, $request->get('secret'));
    }



    private function generateLogin($prefix) {
        do {
            $login = $prefix.'_'.$this->randomStr();
        } while (User::where('login', $login)->first());
        return $login;
    }


    private function randomStr($l = 10) {
        $c = '0123456789abcdefghijklmnopqrstuvwxyz';
        return substr(str_shuffle(str_repeat($c, 5)), 0, $l);
    }


    private function makeResponse($res, $secret) {
        $res = json_encode($res);
        $res = openssl_encrypt($res, 'AES-128-ECB', $secret);
        return response($res);
    }

}