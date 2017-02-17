<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountController extends Controller
{

    public function index(Request $request)
    {
        return view('account.index', [
            'redirect_uri' => $request->get('redirect_uri'),
            'user' => \Auth::user()
        ]);
    }


    public function updateAccount(Request $request) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.\Auth::user()->id
        ]);
        \Auth::user()->update($request->only(['name', 'email']));
        return $this->getRedirectAfterUpdate($request);
    }


    public function updatePassword(Request $request) {
        $this->validate($request, [
            'password' => 'required|min:6|confirmed'
        ]);
        \Auth::user()->password = \Hash::make($request->input('password'));
        \Auth::user()->save();
        return $this->getRedirectAfterUpdate($request);
    }


    private function getRedirectAfterUpdate($request) {
        if($request->has('redirect_uri')) {
            return redirect($request->input('redirect_uri'));
        } else {
            return redirect(route('account'));
        }
    }

}