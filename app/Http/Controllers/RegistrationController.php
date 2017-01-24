<?php

namespace App\Http\Controllers;

use Request;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\OAuthFrontendClient;

class RegistrationController extends Controller
{

    use OAuthFrontendClient;

    private $validator_rules = [
        'email' => 'required|email|unique:users',
        'password' => 'required|confirmed',
        'password_confirmation' => 'required'
    ];

    public function register(Request $request) {
        $this->validate($request, $this->validator_rules);
        $user_data = $request->all();
        $user_data['password'] = Hash::make($user_data['password']);
        $user = User::create($user_data);
        $token = $this->issueAccessToken($user);
        return response()->json([
            'success' => true,
            'token' => $token
        ]);
    }

}