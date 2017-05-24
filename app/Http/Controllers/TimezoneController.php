<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TimezoneController extends Controller
{

    public function index(Request $request) {
        return response()->json(
            timezone_name_from_abbr('', 3600 * $request->get('offset'), (int) $request->get('dls'))
        );
    }

}
