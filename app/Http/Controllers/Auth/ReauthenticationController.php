<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Reauthentication;
use App\LoginModule\PLatform\PlatformContext;

class ReauthenticationController extends Controller
{


    public function index(PlatformContext $context) {
        return view('reauthentication.index', [
            'cancel_url' => $context->cancelUrl()
        ]);
    }


    public function update(Request $request) {
        return Reauthentication::update($request->input('password'));
    }

}
