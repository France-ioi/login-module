<?php

namespace App\LoginModule\Profile;

use App\LoginModule\Platform\PlatformContext;
use App\Email;

class UserProfile {

    protected $context;


    public function __construct(PlatformContext $context) {
        $this->context = $context;
    }


    public function update($request, $fillable_attributes) {
        $data = $request->only($fillable_attributes);
        $request->user()->fill($data);
        $request->user()->login_revalidate_required = false;
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
            if($email->email != $new_value) {
                $email->email = $new_value;
                $email->requireVerification();
            } else if($verification_code = $request->input($role.'_email_verification_code')) {
                if(!$email->verifyCode($verification_code)) {
                    $errors[$role.'_email_verification_code'] = trans('profile.email_verification_code_error');
                }
            }
            $email->email_revalidate_required = false;
            $email->save();
            return $errors;
        }


        $email = new Email([
            'email' => $new_value,
            'role' => $role
        ]);
        $request->user()->emails()->save($email);
        $email->requireVerification();
        return [];
    }


    public function completed($user) {
        $attributes = SchemaBuilder::availableAttributes();
        if($client = $this->context->client()) {
            $attributes = array_values(array_intersect($client->user_attributes, $attributes));
            foreach($attributes as $attribute) {
                $value = $user->getAttribute($attribute);
                if(is_null($value) || $value == '') {
                    return false;
                }
            }
        }
        return true;
    }

}