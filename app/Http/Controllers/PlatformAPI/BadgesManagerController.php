<?php

namespace App\Http\Controllers\PlatformAPI;

use Illuminate\Http\Request;
//use App\Http\Controllers\Controller;
use App\Badge;

class BadgesManagerController extends PlatformAPIController
{
    public function resetDoNotPossess(Request $request) {
        $res = false;
        if($request->get('client')->badge_api_id) {
            $badge = Badge::where('user_id', $request->get('user_id'))
            ->where('code', $request->get('code'))
            ->where('url', $request->get('client')->badgeApi->url)
            ->where('do_not_possess', true)
            ->first();
        }

        if($badge) {
            $badge->delete();
            $res = true;
        }
        return $this->makeResponse($res, $request->get('client')->secret);
    }

}