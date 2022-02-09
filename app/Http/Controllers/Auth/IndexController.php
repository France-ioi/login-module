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
        $methods = $this->auth_list->split($client ? $client->auth_order : null);
        return view('auth.index', [
            'methods' => $methods,
            'left_panel_visible' => $this->getLeftPanelVisibility($methods),
            'right_panel_visible' => $this->getRightPanelVisibility($methods),
            'platform_name' => $client ? $client->name : trans('app.name')
        ]);
    }


    private function getLeftPanelVisibility($methods) {
        foreach($methods['visible'] as $method) {
            if($method == 'login_email_code') {
                return true;
            }
        }
        return false;
    }

    private function getRightPanelVisibility($methods) {
        foreach($methods['visible'] as $method) {
            if($method != 'login_email_code') {
                return true;
            }
        }
        return count($methods['hidden']) > 0;
    }    
}
