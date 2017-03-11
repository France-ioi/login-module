<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class AccountController extends Controller
{

    public function show(Request $request) {
        $res = $request->user();
        $res['badges'] = $res->badges()->where('do_not_possess', false)->get();
        return response()->json($res);
    }
}
