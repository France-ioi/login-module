<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LoginModule\Platform\PlatformContext;
use App\LoginModule\AuthList;

class IndexController extends Controller
{

    public function __construct(PlatformContext $context,
                                AuthList $auth_list) {
        $this->middleware('guest');
        $this->context = $context;
        $this->auth_list = $auth_list;
    }

    public function index(Request $request) {
        $client = $this->context->client();
        return view('auth.index', [
            'methods' => $this->auth_list->split($client ? $client->auth_order : null),
            'platform_name' => $client ? $client->name : trans('app.name')
        ]);
    }
}
