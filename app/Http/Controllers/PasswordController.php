<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Hash;

class PasswordController extends Controller
{


    public function index(Request $request) {
        return view('password.index');
    }

    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed'
        ]);
        Auth::user()->password = Hash::make($request->input('password'));
        Auth::user()->save();
        return back()->with(['success' => true]);
    }
}
