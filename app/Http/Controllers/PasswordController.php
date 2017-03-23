<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformRequest;
use Auth;

class PasswordController extends Controller
{


    public function index(Request $request) {
        return view('password.index', [
            'cancel_url' => PlatformRequest::getCancelUrl()
        ]);
    }


    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed'
        ]);
        Auth::user()->password = bcrypt($request->input('password'));
        Auth::user()->save();
        Auth::user()->obsolete_passwords()->delete();
        return back()->with(['success' => true]);
    }
}
