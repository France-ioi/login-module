<?php

namespace App\LoginModule\Platform;


class AdminInterface {

    const PATH = 'admin_interface/user/';

    protected $client;

    public function __construct($client) {
        $this->client = $client;
    }    


    public function userLogout($user_id, $redirect_url) {
        return $this->makeURL('logout', $user_id, $redirect_url);
    }


    public function userRefresh($user_id, $redirect_url) {
        return $this->makeURL('refresh', $user_id, $redirect_url);
    }


    private function makeURL($cmd, $user_id, $redirect_url) {
        $params = [
            'user_id' => $user_id,
            'redirect_url' => $redirect_url
        ];
        return $this->client->makeURL(SELF::PATH.$cmd).'?'.http_build_query($params);
    }

}