<?php

namespace App\Http\Controllers;

class TestController extends Controller
{

    public function show() {
        //\DB::enableQueryLog();
        $a = \App\User::with('badges')->find(9)->toArray();
        //dd(\DB::getQueryLog());
        dd($a);
    }

}
