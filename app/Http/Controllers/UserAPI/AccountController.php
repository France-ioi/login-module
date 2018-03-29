<?php

namespace App\Http\Controllers\UserAPI;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class AccountController extends Controller
{

    public function show(Request $request) {
        $client_id = $request->user()->token()->client_id;
        $res = $request->user()->toArray();
        $res['badges'] = $request->user()->badges()->where('do_not_possess', false)->where('url', '<>', '')->get();
        $res['client_id'] = $client_id;
        $platform_group = $request->user()->platformGroups()->where('client_id', $client_id)->first();
        if($platform_group) {
            $res['platform_group_code'] = $platform_group->group_code;
            $platform_group->delete();
        }
        return response()->json($res);
    }

}
