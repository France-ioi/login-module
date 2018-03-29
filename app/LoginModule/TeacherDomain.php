<?php
namespace App\LoginModule;

use App\OfficialDomain;

class TeacherDomain {


    public static function verify($user) {
        if(!$user->country_code) {
            return false;
        }
        $emails = $user->emails()->get()->pluck('email');
        foreach($emails as $email) {
            $domain = self::parseDomain($email);
            if(self::verifyDomain($domain, $user->country_code)) {
                return true;
            }
        }
        return false;
    }


    private static function parseDomain($email) {
        list($tmp, $domain) = explode('@', $email);
        return $domain;
    }


    public static function verifyDomain($domain, $country_code) {
        return (bool) OfficialDomain::where('country_code', $country_code)->where('domain', $domain)->first();
    }

}