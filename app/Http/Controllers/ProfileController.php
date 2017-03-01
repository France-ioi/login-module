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
        if(!$values = PlatformRequest::badge()->restoreUser()) {
            $values = [];
        }
        return view('profile.index', [
            'fields' => PlatformRequest::profileFields()->getEmpty(),
            'values' => $values
        ]);
    }


    public function update(Request $request) {
        $rules = PlatformRequest::profileFields()->getValidationRules();
        if($request->input('country_code') == 'fr') {
            unset($rules['ministry_of_education']);
        }
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
        return redirect(PlatformRequest::authorizationUrl());
    }


}