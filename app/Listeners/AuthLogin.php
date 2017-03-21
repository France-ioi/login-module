<?php

namespace App\Listeners;

use Auth;

class AuthLogin
{

    public function handle($event) {
        $event->user->ip = \Request::ip();
        $event->user->last_login = new \DateTime;
        $event->user->save();
    }


}