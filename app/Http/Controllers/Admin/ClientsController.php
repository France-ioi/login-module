<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Client;

class ClientsController extends Controller
{

    public function index() {
        return view('admin.clients.index', [
            'clients' => Client::get()
        ]);
    }


    public function edit($id) {
        return view('admin.clients.form', [
            'client' => Client::findOrFail($id)
        ]);
    }
}
