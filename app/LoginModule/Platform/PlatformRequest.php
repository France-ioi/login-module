<?php

namespace App\LoginModule\Platform;

use App\Client;
use Auth;
use Session;

class PlatformRequest
{

    const REDIRECT_URL_KEY = 'platform_redirect_url';
    const CLIENT_ID_KEY = 'platform_client_id';

    private static $client = null;
    private static $client_available = true;
    private static $auth_order = null;
    private static $profile_fields = null;
    private static $badge = null;


    static function cacheToSession($request) {
        if($request->get('redirect_uri') && $request->get('client_id')) {
            $query = parse_url($request->fullUrl(), PHP_URL_QUERY);
            parse_str($query, $params);

            if($request->is('oauth/authorize')) {
                self::setRedirectUrl($request->fullUrl());
            } else {
                self::setRedirectUrl($params['redirect_uri']);
            }
            Session::put(self::CLIENT_ID_KEY, $params['client_id']);
        }
    }


    static function hasRedirectUrl() {
        return Session::has(self::REDIRECT_URL_KEY);
    }


    static function setRedirectUrl($url) {
        Session::put(self::REDIRECT_URL_KEY, $url);
    }


    static function getRedirectUrl($alternative = '/account') {
        if(!$url = Session::get(self::REDIRECT_URL_KEY)) {
            $url = $alternative;
        }
        return url($url);
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