<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;

class AccountController extends Controller
{


    public function index(Request $request, PlatformContext $context) {
        $client = $context->client();
        return view('account.index', [
            'need_badge_verification' => (bool) $context->badge()->api(),
            'platform_name' => $client ? $client->name : trans('app.name')
        ]);
    }

}