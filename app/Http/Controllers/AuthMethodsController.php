<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\OAuthClient\Manager;

class AuthMethodsController extends Controller
{

    public function index() {
        return view('auth_methods.index', [
            'providers'  => Manager::providers(),
            'connected' => Auth::user()->auth_connections()->get()->pluck('id', 'provider')->toArray(),
            'badges' => Auth::user()->badges()->where('do_not_possess', false)->get()
        ]);
    }


    public function setBadgeLoginAbility($id, $enabled) {
        $badge = Auth::user()->badges()->findOrFail($id);
        $badge->login_enabled = $enabled == 1;
        $badge->save();
        return redirect('/auth_methods');
    }

}
