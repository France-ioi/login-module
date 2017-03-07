<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;

class AccountController extends Controller
{

    public function show(Request $request) {
        return response()->json(
            User::with('badges')->findOrFail($request->user()->id)
        );
    }
}
