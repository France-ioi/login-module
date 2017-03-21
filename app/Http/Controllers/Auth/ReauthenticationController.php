<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Platform\PlatformRequest;
use App\LoginModule\Reauthentication;

class ReauthenticationController extends Controller
{

    public function index() {
        return view('reauthentication.index', [
            'cancel_url' => PlatformRequest::getCancelUrl()
        ]);
    }


    public function update(Request $request) {
        return Reauthentication::update($request->input('password'));
    }

}
