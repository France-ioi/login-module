<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class PasswordController extends Controller
{


    public function index(Request $request) {
        $request->merge([
            'show_password_form' => '1'
        ]);
        $url = '/auth_methods?'.http_build_query($request->all());
        return redirect($url);
    }


    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed'
        ]);
        $request->user()->password = bcrypt($request->input('password'));
        $request->user()->save();
        $request->user()->obsoletePasswords()->delete();
        return back()->with(['success' => true]);
    }
}
