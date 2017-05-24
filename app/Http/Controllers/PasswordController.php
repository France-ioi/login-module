<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;

class PasswordController extends Controller
{


    public function index(Request $request, PlatformContext $context) {
        return view('password.index', [
            'cancel_url' => $context->cancelUrl()
        ]);
    }


    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed'
        ]);
        $request->user()->password = bcrypt($request->input('password'));
        $request->user()->save();
        $request->user()->obsolete_passwords()->delete();
        return back()->with(['success' => true]);
    }
}
