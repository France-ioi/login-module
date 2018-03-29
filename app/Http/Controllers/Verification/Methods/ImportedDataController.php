<?php

namespace App\Http\Controllers\Verification\Methods;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\VerificationMethod;
use App\Verification;

class ImportedDataController extends Controller
{
    public function index(Request $request) {
        return view('verification.methods.imported_data');
    }

}
