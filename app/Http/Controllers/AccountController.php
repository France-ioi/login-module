<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\LoginModule\Platform\PlatformRequest;

class AccountController extends Controller
{

    public function index(Request $request) {
        return view('account.index', [
            'need_email_verification' => (bool) Auth::user()->emails()->where('verified', false)->first(),
            'need_badge_verification' => (bool) PlatformRequest::badge()->url()
        ]);
    }

}