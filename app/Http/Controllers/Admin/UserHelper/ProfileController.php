<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Email;
use App\UserHelperAction;
use App\LoginModule\Profile\SchemaConfig;

class ProfileController extends UserHelperController
{

    public function index($id, Request $request) {
        $user = $this->getTargetUser($id, $request);
        $user_helper = $request->user()->userHelper;
        return view('admin.user_helper.profile', [
            'user' => $user,
            'user_helper' => $user_helper
        ]);
    }


    public function update($id, Request $request) {
        if(!$this->changeAvailable($id, $request)) {
            return view('admin.user_helper.errors.change_limit');
        }
        $user = $this->getTargetUser($id, $request);

        $validator = \Validator::make(
            $request->all(),
            $this->makeValidationRules($request->user(), $user)
        );
        if($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $old_data = $user->toArray();
        $user->fill($this->getProfileAttributes($request));
        $user->save();
        $new_data = $user->toArray();

        $new_data['primary_email'] = $request->get('primary_email');
        $this->updateEmail($user, 'primary', $new_data['primary_email']);
        $new_data['secondary_email'] = $request->get('secondary_email');
        $this->updateEmail($user, 'secondary', $new_data['secondary_email']);

        $details = $this->getDetails($old_data, $new_data);
        if(count($details)) {
            $request->user()->userHelperActions()->save(new UserHelperAction([
                'target_user_id' => $id,
                'type' => 'change',
                'hash' => md5($id.json_encode($details)),
                'details' => $details
            ]));
        }

        return redirect('/admin/user_helper')->with('status', 'User profile updated');
    }


    private function getProfileAttributes($request) {
        $res = [];
        $user_helper = $request->user()->userHelper;
        foreach($user_helper->user_attributes as $attr => $permission) {
            if($permission == 'write') {
                $res[$attr] = $request->get($attr);
            }
        }
        return $res;
    }



    private function updateEmail($user, $role, $value) {
        if(!$value) {
            return;
        }
        if($email = $user->emails()->where('role', $role)->first()) {
            $email->email = $value;
            $email->save();
            return;
        }
        $email = new Email([
            'email' => $value,
            'role' => $role
        ]);
        $user->emails()->save($email);
        return;
    }


    private function changeAvailable($target_user_id, $request) {
        $date = \Carbon\Carbon::now()->subHours(config('user_helper.limits_time_interval'));
        $amount = $request->user()->userHelperActions
            ->where('created_at', '>', $date)
            ->where('type', 'change')
            ->count();
        return $amount < $request->user()->userHelper->changes_amount;
    }


    private function getDetails($old, $new) {
        $res = [];
        foreach($old as $k => $v) {
            if($v != $new[$k]) {
                $res[$k] = [$v, $new[$k]];
            }
        }
        return $res;
    }


    private function makeValidationRules($user, $target_user) {
        $res = [];
        $user_helper = $user->userHelper;
        foreach($user_helper->user_attributes as $attr => $permission) {
            if($permission == 'write') {
                $config = SchemaConfig::$attr($target_user);
                if(isset($config['valid'])) {
                    $res[$attr] = (array) $config['valid'];
                }
            }
        }
        return $res;
    }

}