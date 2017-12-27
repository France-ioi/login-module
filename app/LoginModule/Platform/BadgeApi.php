<?php

namespace App\LoginModule\Platform;

use App\Badge;

class BadgeApi {

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
            'action' => 'verifyCode',
            'code' => $code
        ];
        $res = self::post($url, $request);
        if($res && is_array($res) && !isset($res['error'])) {
            return [
                'login' => array_get($res, 'sLogin'),
                'email' => array_get($res, 'sEmail'),
                'first_name' => array_get($res, 'sFirstName'),
                'last_name' => array_get($res, 'sLastName'),
                'student_id' => array_get($res, 'sStudentId'),
                'gender' => strtolower(substr(array_get($res, 'sSex'), 0, 1)),
                'data' => array_get($res, 'data')
            ];
        }
        return false;
    }


    static function update($url, $code, $user_id) {
        if(!self::callableUrl($url)) {
            return true;
        }
        $post_data = [
            'action' => 'updateInfos',
            'code' => $code,
            'idUser' => $user_id
        ];
        $res = self::post($url, $post_data);
        return $res && $res['success'];
    }


    static function remove($url, $code) {
        if(!self::callableUrl($url)) {
            return true;
        }
        $request = [
            'action' => 'removeByCode',
            'code' => $code
        ];
        $res = self::post($url, $request);
        return $res && $res['success'];
    }


}
