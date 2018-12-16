<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\UserHelperAction;

class ProfileController extends Controller
{

    public function index($id, Request $request) {
        $user_helper = $request->user()->userHelper;
        return view('admin.user_helper.profile', [
            'user' => User::findOrFail($id),
            'user_helper' => $user_helper
        ]);
    }


    public function update($id, Request $request) {
        $user = User::findOrFail($id);
        $old_data = $user->toArray();
        $user->fill($this->getProfileAttributes($request));
        $user->save();
        $new_data = $user->toArray();

        $request->user()->userHelperActions()->save(new UserHelperAction([
            'target_user_id' => $id,
            'details' => [
                'action' => 'update_profile',
                'changes' => array_diff_assoc($old_data, $new_data)
            ]
        ]));
        return redirect('/admin/user_helper')->with('status', 'User profile updated');
    }


    private function getProfileAttributes($request) {
        $res = [];
        $user_helper = $request->user()->userHelper;
        if($user_helper) {
            foreach($user_helper->user_attributes as $attr => $permission) {
                if($permission == 'write') {
                    $res[$attr] = $request->get($attr);
                }
            }
        }

        return $res;
    }

}