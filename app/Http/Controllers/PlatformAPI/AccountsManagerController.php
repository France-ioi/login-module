<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\LoginModule\AccountsManager;
use Illuminate\Support\Facades\Validator;
use App\LoginModule\UserDataGenerator;

class AccountsManagerController extends PlatformAPIController
{

    protected $accounts_manager;

    public function __construct(AccountsManager $accounts_manager, UserDataGenerator $generator) {
        $this->accounts_manager = $accounts_manager;
        $this->generator = $generator;
    }


    private function validatorCreate(array $data) {
        return Validator::make($data, [
            'prefix' => 'required|min:1|max:100',
            'amount' => 'required|integer|min:1',
            'postfix_length' => 'required|integer|min:3|max:50',
            'password_length' => 'required|integer|min:6|max:50',
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
        $params = $request->all();
        $postfix_length = isset($params['postfix_length']) ? $params['postfix_length'] : 8;
        $logins = $this->generator->batchLogins($request->get('amount'), $params['prefix'], $postfix_length);
        if(!$logins) {
            $res = [
                'success' => false,
                'error' => 'Login generation error'
            ];
            return $this->makeResponse($res, $request->get('client')->secret);
        }
        for($i=0; $i<count($logins); $i++) {
            $params['login'] = $logins[$i];
            $users[] = $this->accounts_manager->create($params);
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
        $this->accounts_manager->delete($request->all());
        $res = [
            'success' => true
        ];
        return $this->makeResponse($res, $request->get('client')->secret);
    }

}