<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Requests\Admin\UserHelper\UpdatePasswordRequest;
use App\UserHelperAction;

class PasswordController extends UserHelperController
{

    public function index($id, Request $request) {
        $user = $this->getTargetUser($id, $request);
        return view('admin.user_helper.password', [
            'user' => $user
        ]);
    }

    public function update($id, UpdatePasswordRequest $request) {
        $user = $this->getTargetUser($id, $request);
        $user->update([
            'password' => \Hash::make($request->input('password'))
        ]);
        $request->user()->userHelperActions()->save(new UserHelperAction([
            'target_user_id' => $id,
            'type' => 'password',
            'hash' => md5($id.$request->input('password'))
        ]));
        return redirect('/admin/user_helper')->with('status', 'Password changed');
    }

}
