<?php

namespace App\LoginModule\Bebras\Mappers;

class UserMapper {

    static function remap($row) {
        $res = [
            'gender' => null,
            'primary_email_verified' => !empty($row->officialEmailValidated),
            'secondary_email_verified' => !empty($row->alternativeEmailValidated),
        ];
        $map = [
            'ID' => 'id',
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'officialEmail' => 'primary_email',
            'alternativeEmail' => 'secondary_email',
            'comment' => 'presentation',
            'password' => 'passwordMd5'
        ];
        foreach($map as $v1 => $v2) {
            $res[$v2] = isset($row->$v1) ? $row->$v1 : null;
        }
        if($row->gender == 'm') {
            $res['gender'] = 'M';
        } else if($row->gender == 'f') {
            $res['gender'] = 'F';
        }
        return $res;
    }

}