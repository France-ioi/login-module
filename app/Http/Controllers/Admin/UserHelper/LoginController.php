<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\User;

class LoginController extends Controller
{
    public function login($id, Request $request) {
        $user = User::findOrFail($id);
        if(!$request->user()->userHelperClients->pluck('id')->search($user->creator_client_id)) {
            abort(403);
        }
        session()->flash('skip_auth_login_event', true);
        Auth::login($user);
        return redirect('/account');
    }
}
