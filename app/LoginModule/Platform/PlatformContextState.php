<?php

namespace App\LoginModule\Platform;

class PlatformContextState
{

    const SESSION_KEY = 'PLATFORM_CONTEXT';

    const DEFAULT_DATA = [
        'client_id' => null,
        'redirect_uri' => null,
        'cancelable' => false
    ];

    protected $data;
    protected $session = null;

    public function __construct() {
        $this->data = self::DEFAULT_DATA;
    }


    public function session($session) {
        $this->session = $session;
        $this->data = array_replace(self::DEFAULT_DATA, $this->session->get(self::SESSION_KEY) ?: []);
    }


    public function get($key = null) {
        if(is_null($key)) {
            return $this->data;
        }
        return isset($this->data[$key]) ? $this->data[$key] : null;
    }


    public function set(array $data) {
        $this->data = $data;
        $this->session && $this->session->put(self::SESSION_KEY, $data);
    }


    public function flush() {
        $this->data = self::DEFAULT_DATA;
        $this->session && $this->session->forget(self::SESSION_KEY);
    }

}