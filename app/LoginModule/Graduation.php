<?php

namespace App\LoginModule;

use Carbon\Carbon;

class Graduation
{

    public static function month($country = null) {
        if($country && $month = config('graduation_month.'.$country)) {
            return $month;
        }
        return config('graduation_month.default');
    }


    public static function year($user) {
        if(!$user || !is_numeric($user->graduation_grade)) {
            return null;
        }
        $ofs = (int) $user->graduation_grade;
        if($ofs < 0) return null;
        $date = self::gradeExpirationDate($user);
        return $date->year + $ofs;
    }


    public static function gradeExpirationDate($user) {
        $month = self::month($user ? $user->country_code : null);
        $date = Carbon::now();
        $date->day = 1;
        if($date->month >= $month) {
            $date->year++;
        }
        $date->month = $month;
        return $date;
    }

}