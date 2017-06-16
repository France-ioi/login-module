<?php

namespace App\LoginModule\Profile;

use App\LoginModule\Platform\PlatformContext;


class UserProfile {

    protected $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function update($request, $fillable_attributes) {
        $data = $request->only($fillable_attributes);
        $request->user()->fill($data);
        $request->user()->save();

        $errors = [];
        foreach(['primary', 'secondary'] as $role) {
            if(array_search($role.'_email', $fillable_attributes) !== false) {
                $errors = array_merge($errors, $this->updateEmail($request, $role));
            }
        }
        return count($errors) > 0 ? $errors : true;
    }


    private function updateEmail($request, $role) {
        $new_value = $request->input($role.'_email');

        if(!$new_value) {
            $request->user()->emails()->where('role', $role)->delete();
            return [];
        }

        if($email = $request->user()->emails()->where('role', $role)->first()) {
            $errors = [];
            if($verification_code = $request->input($role.'_email_verification_code')) {
                if(!$email->verifyCode($verification_code)) {
                    $errors[$role.'_email_verification_code'] = trans('profile.email_verification_code_error');
                }
            }
            if($new_value) {
                $email->email = $new_value;
            }
            $email->save();
            return $errors;
        }

        if($new_value) {
            $email = new Email([
                'email' => $new_value,
                'role' => $role
            ]);
            $request->user()->emails()->save($email);
        }
        return [];
    }


    public function completed($user) {
        if($client = $this->context->client()) {
            foreach($client->user_attributes as $attribute) {
                $value = $user->getAttribute($attribute);
                if(is_null($value) || $value == '') {
                    return false;
                }
            }
        }
        return true;
    }

}