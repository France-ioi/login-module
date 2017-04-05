<?php

namespace App\Listeners;

use App\AccessTokenCounter;

class AccessTokenCreated
{

    public function handle($event) {
        if($counter = AccessTokenCounter::where('user_id', $event->userId)->where('client_id', $event->clientId)->first()) {
            $counter->total++;
            $counter->last_created_at = new \DateTime;
            $counter->save();
        } else {
            AccessTokenCounter::create([
                'user_id' => $event->userId,
                'client_id' => $event->clientId,
                'last_created_at' => new \DateTime,
                'total' => 1
            ]);
        }
    }

}