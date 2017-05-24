<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
//use App\LoginModule\Platform\PlatformRequest;
use App\Email;
use Session;
use App\OAuthClient\Manager;


class ProfileController extends Controller
{



    public function index(Request $request) {
        $user = $request->user();
        if($badge_data = $this->context->badge()->restoreData()) {
            $user->fill($badge_data['user']);
        }

        if(count($fixed = $this->fixedAttributes($user)) > 0) {
            if($redirect = $request->get('redirect_uri')) {
                // Redirect to callback_profile from the platform after showing the dialog
                session()->put('url.intended', $request->get('redirect_uri'));
            }
        }


        $required = PlatformRequest::profileFields()->getRequired();
        if($request->has('all')) {
            $fields = PlatformRequest::profileFields()->getAll();
            if(array_search('login', $required) === false) {
                $fields = array_diff($fields, ['login']);
            }
        } else {
            $fields = $required;
        }

        $verifiable = PlatformRequest::profileFields()->getVerifiable();

        return view('profile.index', [
            'fields' => $this->profile->form()->fields(),
            'fixed' => array_fill_keys($fixed, true),
            'starred' => array_fill_keys($this->profile->starred(), true),
            /*
            'required' => array_fill_keys($required, true),
            'verifiable' => array_fill_keys($verifiable, true),
            */
            'star' => config('ui.star'),
            'user' => $user,
            'all' => $request->get('all'),
            'cancel_url' => $this->context->cancelUrl()
        ]);
    }


    public function update() {
        $fixed = Manager::provider('pms')->getFixedFields();
        $fixed = array_flip($fixed);
        $required = PlatformRequest::profileFields()->getRequired();
        $rules = PlatformRequest::profileFields()->getValidationRules($required);
        $rules = array_diff_key($rules, $fixed);
        $this->validate($request, $rules);

        Auth::user()->fill($request->except($fixed));
        Auth::user()->save();

        $errors = [];
        if(!$this->updateUserEmail('primary', $request, !isset($fixed['primary_email']))) {
            $errors['primary_email_validation_code'] = trans('profile.primary_email').trans('profile.email_verification_failed');
        }
        if(!$this->updateUserEmail('secondary', $request, !isset($fixed['secondary_email']))) {
            $errors['secondary_email_verification_code'] = trans('profile.secondary_email').trans('profile.email_verification_failed');
        }
        if(!PlatformRequest::profileFields()->verified()) {
            if(!count($errors)) {
                $errors[] = trans('profile.email_verification_required');
            }
            return redirect()->back()->withErrors($errors);
        }
        PlatformRequest::badge()->flushData();
        return redirect(PlatformRequest::getRedirectUrl('/profile'));
    }


    private function updateUserEmail($role, $request, $editable) {
        $new_value = $request->input($role.'_email');

        if($editable && !$new_value) {
            Auth::user()->emails()->where('role', $role)->delete();
            return true;
        }

        if($email = Auth::user()->emails()->where('role', $role)->first()) {
            $res = true;
            if($verification_code = $request->input($role.'_email_verification_code')) {
                $res = $email->verifyCode($verification_code);
            }
            if($editable && $new_value) {
                $email->email = $new_value;
            }
            $email->save();
            return $res;
        }

        if($new_value) {
            $email = new Email([
                'email' => $new_value,
                'role' => $role
            ]);
            Auth::user()->emails()->save($email);
        }
        return true;
    }


    private function fixedAttributes($user) {
        if($user->auth_connections()->where('provider', 'pms')->where('active', '1')->first()) {
            return Manager::provider('pms')->getFixedFields();
        }
        return [];
    }

}
