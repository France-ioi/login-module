<?php

namespace App\LoginModule\Platform;


class AdminInterface {

    const PATH = 'user/';

    protected $client;

    public function __construct($client) {
        $this->client = $client;
    }    


    public function available() {
        return $this->client && $this->client->admin_interface_url;
    }


    public function userLogin($user_id) {
        return $this->makeURL('login', $user_id);
    }    


    public function userLogout($user_id, $redirect_url) {
        return $this->makeURL('logout', $user_id, $redirect_url);
    }


    public function userRefresh($user_id, $redirect_url) {
        return $this->makeURL('refresh', $user_id, $redirect_url);
    }


    public function userDelete($user_id, $redirect_url) {
        return $this->makeURL('delete', $user_id, $redirect_url);
    }


    private function makeURL($cmd, $user_id, $redirect_url = null) {
        if(!$this->available()) {
            return false;
        }
        $params = [
            'user_id' => $user_id
        ];
        if($redirect_url !== null) {
            $params['redirect_url'] = $redirect_url;
        }
        return rtrim($this->client->admin_interface_url, '/').
            '/'.SELF::PATH.
            $cmd.
            '?'.http_build_query($params);
    }

}