<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;

class AccountController extends Controller
{


    public function index(Request $request, PlatformContext $context) {
        return view('account.index', [
            'need_badge_verification' => (bool) $context->badge()->url()
        ]);
    }

}