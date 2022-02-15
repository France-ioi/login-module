<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\Platform\PlatformUser;

class BanController extends Controller
{
    

    public function index(PlatformContext $context, Request $request) {
        $client = $context->client();
        $link = PlatformUser::link($client->id, $request->user()->id);
        if(!$link || !$link->banned) {
            return redirect('/profile');
        }
        return view('ban.index', [
            'client' => $context->client()
        ]);
    }

}
