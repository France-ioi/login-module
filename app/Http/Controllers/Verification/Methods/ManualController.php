<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;
use App\LoginModule\Platform\PlatformContext;

class ManualController extends Controller
{
    public function index(Request $request, PlatformContext $context) {
        return view('verification.methods.manual', [
            'client' => $context->client()
        ]);
    }


}
