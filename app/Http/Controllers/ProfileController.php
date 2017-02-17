<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\LoginModule\Platform\Platform;
use App\Email;

class ProfileController extends Controller
{


    public function index(Request $request) {
        return view('profile.index', [
            'fields' => Platform::profileFields(Auth::user())->getEmpty()
        ]);
    }


    public function update(Request $request) {
        $rules = Platform::profileFields(Auth::user())->getValidationRules();
        $this->validate($request, $rules);
        Auth::user()->fill($request->except(['primary_email', 'secondary_email']));
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
        }
        return redirect()->intended('/account');
    }


}