<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AccountController extends Controller
{

    public function show(Request $request) {
        return response()->json(
            \App\User::with('badges')->findOrFail($request->user()->id)
        );
    }
}
