<?php

namespace App\LoginModule\Platform;

use App\Client;
use Auth;
use Request;
use Session;

class Platform
{

    private static $client = null;
    private static $client_available = true;


    static function authOrder() {
        return new AuthOrder(self::getClient());
    }


    static function profileFields($user = null) {
        return new ProfileFields(self::getClient(), $user);
    }


    static function getClient() {
        if(self::$client_available) {
            if(!self::$client) {
                $client_id = self::getClientIdFromUrl(Request::fullUrl());
                if(!$client_id) {
                    $client_id = self::getClientIdFromUrl(Session::get('url.intended'));
                }
                if($client_id) {
                    self::$client = Client::find($client_id);
                } else {
                    self::$client_available = false;
                }
            }
            return self::$client;
        }
        return null;
    }


    static function getClientIdFromUrl($url) {
        $query = parse_url($url, PHP_URL_QUERY);
        parse_str($query, $res);
        return isset($res['client_id']) ? $res['client_id'] : null;
    }

}