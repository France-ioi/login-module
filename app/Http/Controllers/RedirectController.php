<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Platform\PlatformContext;

class RedirectController extends Controller
{

    public function __construct(PlatformContext $context) {
        $this->context = $context;    
    }
    
    public function continue(Request $request) {
        return redirect($this->context->continueUrl($request->get('alternative_url')));
    }
}
