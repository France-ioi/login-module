<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use Auth;


class LoginController extends UserHelperController
{

    public function login($id, Request $request) {
        $user = $this->getTargetUser($id, $request);
        session()->flash('skip_auth_login_event', true);
        Auth::login($user);
        return redirect('/account');
    }

}