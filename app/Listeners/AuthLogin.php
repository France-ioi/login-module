<?php

namespace App\Listeners;

class AuthLogin
{

    public function handle($event) {
        $event->user->ip = $this->getIp();
        $event->user->last_login = new \DateTime;
        $event->user->save();
    }

    private function getIp() {
        return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : \Request::ip();
    }

}