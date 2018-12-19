<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;


class DetailsController extends Controller
{

    public function index($id, Request $request) {
        $user = User::findOrFail($id);
        $user_helper = $request->user()->userHelper;
        if(!$request->user()->userHelperClients->pluck('id')->search($user->creator_client_id)) {
            abort(403);
        }
        return view('admin.user_helper.details', [
            'user' => $user
        ]);
    }

}