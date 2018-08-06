<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AuthOrderMerge extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Client::get()->each(function($client) {
            $client->auth_order = array_values(array_filter($client->auth_order, function($method) {
                return $method !== 'badge';
            }));
            $client->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        App\Client::get()->each(function($client) {
            $idx1 = array_search('login', $client->auth_order);
            $idx2 = array_search('badge', $client->auth_order);
            if($idx1 !== false && $idx2 === false) {
                $order = array_values($client->auth_order);
                array_splice($order, $idx1 + 1, 0, 'badge');
                $client->auth_order = $order;
                $client->save();
            }
        });
    }
}
