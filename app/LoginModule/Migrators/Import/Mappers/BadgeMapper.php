<?php

namespace App\LoginModule\Migrators\Import\Mappers;

class BadgeMapper {

    static function remap($row) {
        $res = [
            'id' => $row->id,
            'user_id' => $row->idUser,
            'url' => $row->sBadge,
            'code' => null,
            'do_not_possess' => $row->bDoNotPossess == 1
        ];
        $infos  = json_decode($row->jBadgeInfos, true);
        if(json_last_error() === JSON_ERROR_NONE && isset($infos['code'])) {
            $res['code'] = $infos['code'];
        }
        return $res;
    }

}