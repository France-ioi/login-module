<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\LoginModule\Platform\PlatformRequest;
use App\Email;
use Session;

class ProfileController extends Controller
{


    public function index(Request $request) {
        $user = Auth::check() ? Auth::user() : new User;
        if($badge_data = PlatformRequest::badge()->restoreData()) {
            $user->fill($badge_data['user']);
        }

        if($user->auth_connections()->where('provider', 'pms')->where('active', '1')->first()) {
            if($redirect = $request->get('redirect_uri')) {
                // Redirect to callback_profile from the platform after showing the dialog
                Session::put('url.intended', $request->get('redirect_uri'));
            }
            return view('profile.pms');
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
            'fields' => $fields,
            'required' => array_fill_keys($required, true),
            'verifiable' => array_fill_keys($verifiable, true),
            'star' => config('ui.star'),
            'user' => $user,
            'all' => $request->get('all'),
            'cancel_url' => PlatformRequest::getCancelUrl()
        ]);
    }



    public function update(Request $request) {
        $this->validateRequest($request);
        Auth::user()->fill($request->all());
        Auth::user()->save();

        $errors = [];
        if(!$this->updateUserEmail('primary', $request)) {
            $errors['primary_email_validation_code'] = trans('profile.primary_email').trans('profile.email_verification_failed');
        }
        if(!$this->updateUserEmail('secondary', $request)) {
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


    private function validateRequest($request) {
        $required = PlatformRequest::profileFields()->getRequired();
        $rules = PlatformRequest::profileFields()->getValidationRules($required);
        $this->validate($request, $rules);
    }


    private function updateUserEmail($role, $request) {
        $res = true;
        if($new_value = $request->input($role.'_email')) {
            if($email = Auth::user()->emails()->where('role', $role)->first()) {
                if($verification_code = $request->input($role.'_email_verification_code')) {
                    $res = $email->verifyCode($verification_code);
                }
                $email->email = $new_value;
                $email->save();
            } else {
                $email = new Email([
                    'email' => $new_value,
                    'role' => $role
                ]);
                Auth::user()->emails()->save($email);
            }
        } else {
            Auth::user()->emails()->where('role', $role)->delete();
        }
        return $res;
    }


    public function timezone(Request $request) {
        return response()->json(
            timezone_name_from_abbr('', 3600 * $request->get('offset'), (int) $request->get('dls'))
        );
    }

}
