<?php

namespace App\LoginModule\Platform;

use Session;

class Badge {

    const SESSION_KEY = 'BADGE';
    protected $client;

    public function __construct($client, $user) {
        $this->client = $client;
        $this->user = $user;
    }


    public function url() {
        return $this->client && $this->client->badge_api_id ? $this->client->badgeApi->url : null;
    }


    public function required() {
        return $this->client ? $this->client->badge_required : false;
    }


    public function valid() {
        $url = $this->url();
        if(!$url || !$this->required()) {
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
        $auth_enabled = $this->client && $this->client->badge_api_id ? $this->client->badgeApi->auth_enabled : false;
        if(!$auth_enabled) {
            return false;
        }
        if($badge_data = $this->verify($code)) {
            Session::put(self::SESSION_KEY, $badge_data);
            return $badge_data;
        } else {
            $this->flushData();
        }
        return false;
    }


    public function update($code, $user_id) {
        if($url = $this->url()) {
            BadgeApi::update($url, $code, $user_id);
        }
    }

    public function restoreData() {
        return Session::get(self::SESSION_KEY);
    }


    public function flushData() {
        Session::forget(self::SESSION_KEY);
    }

}