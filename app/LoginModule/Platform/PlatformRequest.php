<?php

namespace App\LoginModule\Platform;

use App\Client;
use Auth;
use Session;

class PlatformRequest
{

    const AUTHORIZATION_URL_KEY = 'authorization_url';
    const CLIENT_ID_KEY = 'client_id';

    private static $client = null;
    private static $client_available = true;
    private static $auth_order = null;
    private static $profile_fields = null;
    private static $badge = null;


    static function cacheToSession($request) {
        if(!$request->is('oauth/authorize')) return;
        Session::put(self::AUTHORIZATION_URL_KEY, $request->fullUrl());
        $query = parse_url($request->fullUrl(), PHP_URL_QUERY);
        parse_str($query, $params);
        if(isset($params['client_id'])) {
            Session::put(self::CLIENT_ID_KEY, $params['client_id']);
        } else {
            Session::forget(self::CLIENT_ID_KEY);
        }
    }


    static function authorizationUrl() {
        $url = Session::get(self::AUTHORIZATION_URL_KEY);
        return url($url ? $url : '/account');
    }


    static function getClient() {
        if(self::$client_available) {
            if(!self::$client) {
                self::$client = Client::find(Session::get(self::CLIENT_ID_KEY));
            }
            self::$client_available = (bool) self::$client;
            return self::$client;
        }
        return null;
    }


    static function authOrder() {
        if(!self::$auth_order) {
            self::$auth_order = new AuthOrder(self::getClient());
        }
        return self::$auth_order;
    }


    static function profileFields($user = null) {
        if(!self::$profile_fields) {
            self::$profile_fields = new ProfileFields(self::getClient(), Auth::user());
        }
        return self::$profile_fields;
    }


    static function badge() {
        if(!self::$badge) {
            self::$badge = new Badge(self::getClient(), Auth::user());
        }
        return self::$badge;
    }

}