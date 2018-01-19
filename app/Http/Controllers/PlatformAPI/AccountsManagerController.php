<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Badge;
use App\User;
use App\AutoLoginToken;
use App\LoginModule\UserDataGenerator;
use Illuminate\Support\Facades\Validator;

class AccountsManagerController extends PlatformAPIController
{

    protected $generator;


    public function __construct(UserDataGenerator $generator) {
        $this->generator = $generator;
    }


    private function validatorCreate(array $data) {
        return Validator::make($data, [
            'prefix' => 'required|min:1|max:100',
            'amount' => 'required|integer|min:1|max:50',
            'postfix_length' => 'integer|min:3|max:50',
            'password_length' => 'integer|min:6|max:50',
        ]);
    }


    public function create(Request $request) {
        $validator = $this->validatorCreate($request->all());
        if($validator->fails()) {
            $res = [
                'success' => false,
                'error' => 'Wrong params'
            ];
            return $this->makeResponse($res, $request->get('client')->secret);
        }


        $users = [];
        for($i=0; $i<$request->get('amount'); $i++) {
            $data = [
                'password' => $this->generator->password($request->get('password_length')),
                'login' => $this->generator->login($request->get('prefix'), $request->get('postfix_length'))
            ];

            if($data['login'] === null) {
                $res = [
                    'success' => false,
                    'error' => 'Login generation error'
                ];
                return $this->makeResponse($res, $request->get('client')->secret);
            }

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
            $users[] = $data;
        }
        $res = [
            'success' => true,
            'data' => $users
        ];
        return $this->makeResponse($res, $request->get('client')->secret);
    }


    private function validatorDelete(array $data) {
        return Validator::make($data, [
            'prefix' => 'required|min:1|max:100'
        ]);
    }


    public function delete(Request $request) {
        $validator = $this->validatorDelete($request->all());
        if($validator->fails()) {
            $res = [
                'success' => false,
                'error' => 'Wrong params'
            ];
            return $this->makeResponse($res, $request->get('client')->secret);
        }

        if(!empty($request->get('prefix'))) {
            $prefix = str_replace('_', '\_', $request->get('prefix')).'%';
            User::where('login', 'like', $prefix)->where('creator_client_id', $request->get('client_id'))->delete();
        }
        $res = [
            'success' => true
        ];
        return $this->makeResponse($res, $request->get('client')->secret);
    }

}