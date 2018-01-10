<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Badge;
use App\User;
use App\AutoLoginToken;
use App\LoginModule\UserDataGenerator;

class AccountsManagerController extends PlatformAPIController
{

    protected $generator;


    public function __construct(UserDataGenerator $generator) {
        $this->generator = $generator;
    }



    public function create(Request $request) {
        $res = [];
        for($i=0; $i<$request->get('amount'); $i++) {
            $data = [
                'password' => $this->generator->password(),
                'login' => $this->generator->login($request->get('prefix'))
            ];

            $user = new User([
                'login' => $data['login'],
                'password' => \Hash::make($data['password'])
            ]);
            $user->login_fixed = (bool) $request->get('login_fixed');
            $user->creator_client_id = $request->get('client_id');
            $user->save();
            $data['id'] = $user->id;

            if($request->get('auto_login')) {
                $data['auto_login_token'] = $this->generator->autoLoginToken();
                $user->autoLoginToken()->save(new AutoLoginToken([
                    'token' => $data['auto_login_token']
                ]));
            }

            if($request->get('participation_code')) {
                $data['participation_code'] = $this->generator->participationCode();
                $user->badges()->save(new Badge([
                    'url' => '',
                    'code' => $data['participation_code'],
                    'login_enabled' => true,
                    'data' => [
                        // save for future?
                        'type' => 'participation_code',
                        'client_id' => $request->get('client_id')
                    ]
                ]));
            }
            $res[] = $data;
        }
        return $this->makeResponse($res, $request->get('client')->secret);
    }


    public function delete(Request $request) {
        if(!empty($request->get('prefix'))) {
            $prefix = str_replace('_', '\_', $request->get('prefix')).'%';
            User::where('login', 'like', $prefix)->where('creator_client_id', $request->get('client_id'))->delete();
        }
        return $this->makeResponse(true, $request->get('client')->secret);
    }

}