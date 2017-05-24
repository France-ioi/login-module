<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\OAuthClient\Manager;
use App\LoginModule\Platform\PlatformContext;

class AuthMethodsController extends Controller
{


    public function index(PlatformContext $context) {
        return view('auth_methods.index', [
            'providers'  => Manager::providers(),
            'support_remove'  => array_flip(Manager::SUPPORT_REMOVE),
            'connected' => Auth::user()->auth_connections()->get()->pluck('id', 'provider')->toArray(),
            'badges' => Auth::user()->badges()->where('do_not_possess', false)->get(),
            'cancel_url' => $context->cancelUrl(),
            'has_password' => Auth::user()->has_password
        ]);
    }


    public function setBadgeLoginAbility($id, $enabled) {
        $badge = Auth::user()->badges()->where('code', '<>', '')->findOrFail($id);
        $badge->login_enabled = $enabled == 1;
        $badge->save();
        return redirect('/auth_methods');
    }

}
