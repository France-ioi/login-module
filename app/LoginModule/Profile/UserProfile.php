<?php

namespace App\LoginModule\Profile;

use App\LoginModule\Platform\PlatformContext;
use App\Email;
use Carbon\Carbon;

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

        if($request->hasFile('picture')) {
            $this->storePicture($request->user(), $request->file('picture'));
        }

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


    private function storePicture($user, $file) {
        $img = \Image::make($file);
        $pp = config('ui.profile_picture');
        $img->resize($pp['width'], $pp['height'], function($constraint) {
            $constraint->aspectRatio();
        });
        $resource = $img->stream()->detach();
        $path = 'profile_pictures/'.$user->id.'.'.$file->extension();
        if(\Storage::put($path, $resource)) {
            $url = \Storage::url($path);
            $user->picture = asset($path);
        }
        $user->save();
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


    public function getUserBeforeEditor() {
        $user = request()->user();
        if($user->graduation_grade >= 0 &&
            !is_null($user->graduation_grade_expire_at) &&
            Carbon::now()->gte($user->graduation_grade_expire_at)) {
            $user->graduation_grade = null;
            $user->save();
        }
        return $user;
    }

}