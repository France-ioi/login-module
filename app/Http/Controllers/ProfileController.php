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

        $required = PlatformRequest::profileFields()->getRequired();
        if($request->has('all')) {
            $fields = PlatformRequest::profileFields()->getAll();
            if(array_search('login', $required) === false) {
                $fields = array_diff($fields, ['login']);
            }
        } else {
            $fields = $required;
        }
        return view('profile.index', [
            'fields' => $fields,
            'user' => $user,
            'all' => $request->get('all'),
            'cancel_url' => PlatformRequest::getCancelUrl()
        ]);
    }


    public function update(Request $request) {
        $required = PlatformRequest::profileFields()->getRequired();
        $rules = PlatformRequest::profileFields()->getValidationRules($required);
        $this->validate($request, $rules);
        Auth::user()->fill($request->all());
        Auth::user()->save();
        if($request->has('primary_email')) {
            if($primary = Auth::user()->emails()->primary()->first()) {
                $primary->email = $request->input('primary_email');
                $primary->save();
            } else {
                $primary = new Email([
                    'email' => $request->input('primary_email'),
                    'role' => 'primary'
                ]);
                Auth::user()->emails()->save($primary);
            }
        } else {
            Auth::user()->emails()->primary()->delete();
        }
        if($request->has('secondary_email')) {
            if($secondary = Auth::user()->emails()->secondary()->first()) {
                $secondary->email = $request->input('secondary_email');
                $secondary->save();
            } else {
                $secondary = new Email([
                    'email' => $request->input('secondary_email'),
                    'role' => 'secondary'
                ]);
                Auth::user()->emails()->save($secondary);
            }
        } else {
            Auth::user()->emails()->secondary()->delete();
        }
        PlatformRequest::badge()->flushData();
        return redirect(PlatformRequest::getRedirectUrl('/profile'));
    }


    public function timezone(Request $request) {
        return response()->json(
            timezone_name_from_abbr('', 3600 * $request->get('offset'), (int) $request->get('dls'))
        );
    }

}