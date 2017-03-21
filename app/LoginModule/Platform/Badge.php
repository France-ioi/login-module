<?php

namespace App\LoginModule\Platform;

use Session;


class Badge {

    const SESSION_KEY = 'badge_data';
    protected $client;

    public function __construct($client, $user) {
        $this->client = $client;
        $this->user = $user;
    }


    public function url() {
        return $this->client ? $this->client->badge_url : null;
    }


    public function verified() {
        if(!$url = $this->url()) {
            return true;
        }
        $badge = $this->user->badges()->where('url', $url)->first();
        return (bool) $badge;// && $badge->do_not_possess;
    }


    public function verify($code) {
        if($url = $this->url()) {
            if($user = BadgeApi::verify($url, $code)) {
                return [
                    'user' => $user,
                    'code' => $code,
                    'url' => $url
                ];
            }
        }
    }


    public function verifyAndStoreData($code) {
        if($badge_data = $this->verify($code)) {
            Session::put(self::SESSION_KEY, $badge_data);
            return true;
        } else {
            $this->flushData();
        }
    }


    public function restoreData() {
        return Session::get(self::SESSION_KEY);
    }


    public function flushData() {
        Session::forget(self::SESSION_KEY);
    }

}