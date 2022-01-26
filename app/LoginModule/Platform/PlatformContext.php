<?php

namespace App\LoginModule\Platform;

use App\Client;
use App\LoginModule\Locale;
use \Laravel\Passport\TokenRepository;

class PlatformContext
{

    protected $state;
    protected $client;
    protected $badge;
    protected $platform_api;
    protected $platform_authorized;

    public function __construct(PlatformContextState $state) {
        $this->state = $state;
    }


    public function setClientId($client_id) {
        $this->state->set([
            'client_id' => (int) $client_id,
            'redirect_uri' => null,
            'cancelable' => false
        ]);
    }


    public function request($request) {
        $this->state->session($request->session());
        if($request->filled('redirect_uri') && $request->filled('client_id')) {
            if($authorization = $request->is('oauth/authorize')) {
                $this->handlePlatformParams($request);
            }
            $this->state->set([
                'client_id' => (int) $request->get('client_id'),
                'redirect_uri' => $authorization ? $request->fullUrl() : $request->get('redirect_uri'),
                'cancelable' => !$authorization
            ]);
        } else if(!$request->server('HTTP_REFERER')) {
            //$this->state->flush();
        }
    }


    private function handlePlatformParams($request) {
        if($request->filled('locale')) {
            Locale::setIfEmpty($request->get('locale'));
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


    public function continueUrl($alternative = '/profile') {
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


    public function platformApi() {
        if(!$this->platform_api) {
            $this->platform_api = new PlatformApi($this->client());
        }
        return $this->platform_api;
    }


    public function platformAuthorized() {
        if($this->platform_authorized === null) {
            $this->platform_authorized = true;
            $client = $this->client();
            $user = auth()->user();
            if($user && $client) {
                $token_repository = new TokenRepository();
                $this->platform_authorized = !!$token_repository->getValidToken($user, $client);
            }
        }
        return $this->platform_authorized;
    }

}