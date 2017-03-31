<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class AccountController extends Controller
{

    public function show(Request $request) {
        //echo($request->user()->token()->client_id);
        $res = $request->user()->toArray();
        $res['badges'] = $request->user()->badges()->where('do_not_possess', false)->get();
        //$res['client_user_id'] = $request->user()->client_user_id;
        return response()->json($res);
    }
}
