<?php

namespace App\Http\Controllers;


class FrontendController extends Controller
{

    public function show() {
        return view('frontend', [
            'config' => []
        ]);
    }

}
