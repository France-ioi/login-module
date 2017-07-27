<?php

namespace App\LoginModule\Platform;

use App\Client;

class PlatformContext
{

    protected $state;
    protected $client;
    protected $badge;


    public function __construct(PlatformContextState $state) {
        $this->state = $state;
    }


    public function request($request) {
        $this->state->session($request->session());
        if($request->has('redirect_uri') && $request->has('client_id')) {
            $authorization = $request->is('oauth/authorize');
            $this->state->set([
                'client_id' => (int) $request->get('client_id'),
                'redirect_uri' => $authorization ? $request->fullUrl() : $request->get('redirect_uri'),
                'cancelable' => !$authorization
            ]);
        } else if(!$request->server('HTTP_REFERER')) {
            $this->state->flush();
        }
    }


    public function getData() {
        return $this->state->get();
    }

    public function setData($data) {
        $this->state->set($data);
    }


    public function client() {
        if(!$this->client && $client_id = $this->state->get('client_id')) {
            $this->client = Client::find($client_id);
        }
        return $this->client;
    }


    public function continueUrl($alternative = '/account') {
        return $this->state->get('redirect_uri') ?: $alternative;
    }


    public function cancelUrl() {
        return $this->state->get('cancelable') ? $this->state->get('redirect_uri') : null;
    }


    public function badge() {
        if(!$this->badge) {
            $this->badge = new Badge($this->client(), auth()->user());
        }
        return $this->badge;
    }

}