<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\OfficialDomain;

class OfficialDomainsController extends Controller
{

    public function index(Request $request) {
        return response()->json(OfficialDomain::where('country_code', $request->get('country_code'))->get()->pluck('domain'));
    }

}
