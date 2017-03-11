<?php

namespace App\LoginModule\DataSync\Mappers;

class UserMapper {

    static function remap($row) {
        $map = [
            'id' => 'id',
            'sPasswordMd5' => 'password',
            'sLogin' => 'login',
            'sFirstName' => 'first_name',
            'sLastName' => 'last_name',
            'sStudentId' => 'student_id',
            'sCountryCode' => 'country_code',
            'sBirthDate' => 'birthday',
            'iGraduationYear' => 'graduation_year',
            'sAddress' => 'address',
            'sZipcode' => 'zipcode',
            'sCity' => 'city',
            'sLandLineNumber' => 'primary_phone',
            'sCellPhoneNumber' => 'secondary_phone',
            'sDefaultLanguage' => 'language',
            'sFreeText' => 'presentation',
            'sWebSite' => 'website',
            'sLastIP' => 'ip',
            'sOpenIdIdentity' => 'uid',
            'sEmail' => 'email',
            'bEmailVerified' => 'email_verified'
        ];
        $res = [];
        foreach($map as $v1 => $v2) {
            $res[$v2] = isset($row->$v1) ? $row->$v1 : null;
        }
        $res['admin'] = $row->bIsAdmin == 1;
        if($row->bIsTeacher == 1) {
            $res['role'] = 'teacher';
        }
        $res['gender'] = null;
        if($row->sSex == 'Male') {
            $res['gender'] = 'm';
        } elseif ($row->sSex == 'Female') {
            $res['gender'] = 'f';
        }
        return $res;
    }

}