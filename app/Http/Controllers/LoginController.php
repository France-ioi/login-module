<?php

namespace App\Http\Controllers;

use Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\OAuthFrontendClient;

class LoginController extends Controller
{

    use OAuthFrontendClient;

    private $validator_rules = [
        'email' => 'required|email',
        'password' => 'required'
    ];


    private function getUser($request) {
        $user = User::where('email', $request->get('email'))->first();
        if($user && Hash::check($request->get('password'), $user->password)) {
            return $user;
        }
        return null;
    }


    public function login(Request $request) {
        $this->validate($request, $this->validator_rules);
        if($user = $this->getUser($request)) {
            if($token_data = $this->issueAccessToken($user)) {
                return response()->json($token_data);
            }
        }
        return response()->json(['password' => 'Wrong credentials'], 422);
    }

}