<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use App\Traits\ProfileCompletion;
use App\Email;

class ProfileController extends Controller
{

    use ProfileCompletion;

    public function index(Request $request) {
        return view('profile.index', [
            'fields' => array_fill_keys($this->getProfileEmptyFields(Auth::user()), true)
        ]);
    }


    public function store(Request $request) {
        $fields = $this->getProfileEmptyFields(Auth::user());
        $validation_rules = $this->getValidationRules($fields);
        $this->validate($request, $validation_rules);
        Auth::user()->fill($request->all());
        Auth::user()->save();
        if($request->has('primary_email')) {
            Auth::user()->emails()->save(new Email([
                'email' => $request->input('primary_email'),
                'role' => 'primary'
            ]));
        }
        if($request->has('secondary_email')) {
            Auth::user()->emails()->save(new Email([
                'email' => $request->input('secondary_email'),
                'role' => 'secondary'
            ]));
        }
        return redirect()->intended('/account');
    }

}
