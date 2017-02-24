<?php

namespace App\LoginModule\Platform;

class AuthOrder {

    protected $client;

    public function __construct($client) {
        $this->client = $client;
    }

    public function get() {
        return $this->client && is_array($this->client->auth_order) ? $this->client->auth_order : null;
    }

}
