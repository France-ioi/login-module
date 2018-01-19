<?php

namespace App\LoginModule\Platform;

use App\Badge;

class PlatformApi {

    static function callableUrl($url) {
        return strpos($url, 'http://') === 0 || strpos($url, 'https://') === 0;
    }


    static function post($url, $request) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($request));
        $res = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($res, true);
        return json_last_error() === JSON_ERROR_NONE ? $res : false;
    }


    static function verify($url, $code) {
        if(!self::callableUrl($url)) {
            return false;
        }
        $request = [
            'action' => 'verify_code',
            'code' => $code
        ];
        $res = self::post($url, $request);
        if($res && !isset($res['error'])) {
            return true;
        }
        return false;
    }


    static function enter($url, $code, $user_id) {
        if(!self::callableUrl($url)) {
            return true;
        }
        $post_data = [
            'action' => 'enter',
            'code' => $code,
            'user_id' => $user_id
        ];
        $res = self::post($url, $post_data);
        return $res && $res['user'];
    }

}
