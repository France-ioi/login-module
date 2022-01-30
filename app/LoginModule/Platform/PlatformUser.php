<?php

namespace App\LoginModule\Platform;

use DateTime;
use Illuminate\Support\Facades\DB;
use App\ClientUser;

class PlatformUser {


    public static function link($client_id, $user_id) {
        $rec = ClientUser::where('client_id', $client_id)->where('user_id', $user_id)->first();
        if(!$rec) {
            $rec = new ClientUser([
                'user_id' => $user_id,
                'client_id' => $client_id
            ]);
        } else if($rec->banned) {
            return $rec;
        }
        $rec->last_activity = new DateTime();
        $rec->save();

        return $rec;
    }


    public static function setBanned($client_id, $user_id, $banned) {
        $rec = ClientUser::where('client_id', $client_id)->where('user_id', $user_id)->first();
        $rec->banned = $banned;
        $rec->save();
    }

}