<?php

namespace App\LoginModule\Platform;

use DateTime;
use Illuminate\Support\Facades\DB;

class PlatformUser {

    public static function link($client_id, $user_id) {
        $data = [
            'user_id' => $user_id,
            'client_id' => $client_id, 
            'last_activity' => new DateTime()
        ];
        DB::table('oauth_client_user')->upsert(
            [$data], 
            ['user_id', 'client_id'],
            ['last_activity']
        );
    }

}