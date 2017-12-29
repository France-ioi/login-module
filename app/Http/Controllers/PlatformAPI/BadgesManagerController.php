<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Badge;

class BadgesManagerController extends Controller
{
    public function resetDoNotPossess(Request $request) {
        $res = false;
        $badge = Badge::where('user_id', $request->get('user_id'))->where('url', $request->get('client')->badge_url)->where('do_not_possess', true)->first();
        if($badge) {
            $badge->delete();
            $res = true;
        }
        return $this->makeResponse($res, $request->get('client')->secret);
    }

    //TODO: remove duplicated code
    private function makeResponse($res, $secret) {
        $res = json_encode($res);
        $res = openssl_encrypt($res, 'AES-128-ECB', $secret);
        return response($res);
    }
}
