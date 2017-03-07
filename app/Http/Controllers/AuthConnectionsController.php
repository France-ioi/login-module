<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\OAuthClient\Manager;

class AuthConnectionsController extends Controller
{

    public function index() {
        return view('auth_connections.index', [
            'providers'  => Manager::providers(),
            'connected' => Auth::user()->auth_connections()->get()->pluck('id', 'provider')->toArray(),
        ]);
    }

}
