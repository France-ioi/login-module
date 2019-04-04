<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Http\Requests\Admin\ResetPasswordsRequest;
use App\LoginModule\UserDataGenerator;

class ResetPasswordsController extends Controller
{

    const DEFAULT_PWD_LENGTH = 8;


    public function __construct(UserDataGenerator $generator) {
        $this->generator = $generator;
    }


    public function index() {
        return view('admin.reset_passwords.index', [
            'pwd_length' => self::DEFAULT_PWD_LENGTH
        ]);
    }


    public function reset(ResetPasswordsRequest $request) {
        $data = [];

        $pwd_length = (int) $request->get('password_length');
        if(!$pwd_length) {
            $pwd_length = self::DEFAULT_PWD_LENGTH;
        }

        $logins = $request->get('logins');
        $logins = preg_split('/\r\n|\n|\r/', $logins);
        $logins = array_filter($logins);
        $logins = array_values($logins);

        return view('admin.reset_passwords.result', [
            'data' => $this->walkUsers($logins, $pwd_length)
        ]);
    }



    private function walkUsers(array $logins, $pwd_length) {
        $res = [];
        foreach($logins as $login) {
            $row = [
                'id' => 'n/a',
                'login' => $login,
                'password' => ''
            ];
            $user = User::where('login', $login)->first();
            if($user) {
                $row['id'] = $user->id;
                $row['password'] = $this->generator->password($pwd_length);
                $user->password = \Hash::make($row['password']);
                $user->save();
            }
            $res[] = $row;
        }
        return $res;
    }

}