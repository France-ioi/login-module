<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class LocaleController extends Controller
{
    public function set($locale) {
        if(Auth::check()) {
            Auth::user()->language = $locale;
            Auth::user()->save();
        } else {
            session()->put('locale', $locale);
        }
        return redirect()->back();
    }
}
