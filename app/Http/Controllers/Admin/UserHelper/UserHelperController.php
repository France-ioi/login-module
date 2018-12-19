<?php

namespace App\Http\Controllers\Admin\UserHelper;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Client;

class UserHelperController extends Controller
{

    public function getTargetUser($id, $request) {
        $user = User::findOrFail($id);

        $alowed_clients = $request->user()->userHelperClients->pluck('id');
        $restricted_clients = Client::where('user_helper_search_exclude', true)->get()->pluck('id');
        $user_clients = $user->accessTokenCounters->pluck('client_id');
        if($alowed_clients->intersect($user_clients)->count() == 0 ||
            $restricted_clients->intersect($user_clients)->count() > 0) {
            abort(403);
        }

        return $user;
    }

}
