<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Locale;

class LocaleController extends Controller
{

    public function set($locale, Request $request) {
        $locale = Locale::set($locale);
        if(auth()->check() && $request->has('update_user')) {
            $user = auth()->user();
            $user->language = $locale;
            $user->save();
        }
        return redirect()->back();
    }

}
