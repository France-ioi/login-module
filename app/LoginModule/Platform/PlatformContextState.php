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



    public function __construct() {
        $this->data = array_replace(self::DEFAULT_DATA, session()->get(self::SESSION_KEY) ?: []);
    }


    public function get($key = null) {
        if(is_null($key)) {
            return $this->data;
        }
        return isset($this->data[$key]) ?: null;
    }


    public function set(array $data) {
        $this->data = $data;
        session()->put(self::SESSION_KEY, $data);
    }


    public function flush() {
        $this->data = self::DEFAULT_DATA;
        session()->forget(self::SESSION_KEY);
    }

}