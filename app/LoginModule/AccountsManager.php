<?php

namespace App\LoginModule;

use App\Badge;
use App\User;
use App\AutoLoginToken;
use App\LoginModule\UserDataGenerator;


class AccountsManager
{

    protected $generator;
    protected $accounts_manager;

    public function __construct() {
        $this->generator = new UserDataGenerator;
    }


    public function create($params) {
        $password_length = isset($params['password_length']) ? $params['password_length'] : 8;
        $postfix_length = isset($params['postfix_length']) ? $params['postfix_length'] : 8;
        $data = [
            'password' => $this->generator->password($password_length),
            'login' => $this->generator->login($params['prefix'], $postfix_length)
        ];
        if($data['login'] === null) {
            return false;
        }
        $user = new User([
            'login' => $data['login'],
            'password' => \Hash::make($data['password'])
        ]);
        $user->login_fixed = isset($params['login_fixed']) && $params['login_fixed'];
        $user->creator_client_id = $params['client_id'];
        if(isset($params['language'])) {
            $user->language = $params['language'];
        }
        $user->save();
        $data['id'] = $user->id;

        if(isset($params['auto_login']) && $params['auto_login']) {
            $data['auto_login_token'] = $this->generator->autoLoginToken();
            $user->autoLoginToken()->save(new AutoLoginToken([
                'token' => $data['auto_login_token']
            ]));
        }

        if(isset($params['participation_code']) && $params['participation_code']) {
            $data['participation_code'] = $this->generator->participationCode();
            $user->badges()->save(new Badge([
                'url' => '',
                'code' => $data['participation_code'],
                'login_enabled' => true,
                'origin_client_id' => $params['client_id'],
                'data' => [
                    // save for future?
                    'type' => 'participation_code',
                    'client_id' => $params['client_id']
                ]
            ]));
        }
        return $data;
    }


    public function delete($params) {
        $prefix = isset($params['prefix']) && !empty($params['prefix']) ? $params['prefix'] : null;
        if($prefix) {
            $prefix = str_replace('_', '\_', $prefix).'%';
            User::where('login', 'like', $prefix)->where('creator_client_id', $params['client_id'])->delete();
        }
    }

}