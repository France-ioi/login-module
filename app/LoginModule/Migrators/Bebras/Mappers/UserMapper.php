<?php

namespace App\LoginModule\Migrators\Bebras\Mappers;

class UserMapper {

    static function remap($row) {
        $res = [
            'gender' => null,
            'verifications' => [
                'primary_email' => !empty($row->officialEmailValidated),
                'secondary_email' => !empty($row->alternativeEmailValidated),
                'role' => false
            ]
        ];
        if($row->isOwnOfficialEmail) {
            // TODO: ask Mathias
            $res['role'] = 'teacher';
            $res['verifications']['role'] = true;
        }
        $map = [
            'ID' => 'bebras_id',
            'externalID' => 'login_module_id',
            'firstName' => 'first_name',
            'lastName' => 'last_name',
            'officialEmail' => 'primary_email',
            'alternativeEmail' => 'secondary_email',
            'comment' => 'presentation',
            'passwordMd5' => 'password',
            'validated' => 'validated'
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