<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Badge;

class BadgesManagerController extends Controller
{
    public function resetDoNotPosess(Request $request) {
        $res = false;
        $badge = Badge::where('user_id', $request->get('user_id'))->where('code', $request->get('code'))->first();
        if($badge) {
            $badge->do_not_possess = false;
            $badge->save();
            $res = true;
        }
        return $this->makeResponse($res, $request->get('secret'));
    }

    //TODO: remove duplicated code
    private function makeResponse($res, $secret) {
        $res = json_encode($res);
        $res = openssl_encrypt($res, 'AES-128-ECB', $secret);
        return response($res);
    }
}
