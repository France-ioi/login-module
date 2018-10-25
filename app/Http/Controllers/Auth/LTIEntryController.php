<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\LTI\LTIHelper;
use App\LoginModule\LTI\LTITokenGenerator;

use Auth;

class LTIEntryController extends Controller
{

    public function handle(Request $request, LTIHelper $lti, LTITokenGenerator $gen) {
        $lc = $lti->handleRequest();
        Auth::login($lc->user);
        if($request->has('redirectUrl')) {
            $token = $gen->generateToken($lc);
            $url = $request->get('redirectUrl');
            $url = $url.(strpos($url, '?') === false ? '?' : '&').'loginToken='.urlencode($token);
        } else {
            $url = '/';
        }
        return redirect($url);
    }




}