<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\LoginModule\Locale;

class LocaleController extends Controller
{

    public function set($locale) {
        Locale::set($locale);
        return redirect()->back();
    }

}
