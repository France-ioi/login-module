<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientsAuthConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        App\Client::get()->each(function($client) {
            if(!count($client->auth_order)) return;
            $order = array_map(function($item) {
                if($item == '_') {
                    return '_HIDDEN';
                } else if($item == 'login') {
                    return 'login_email_code';
                }
                return $item;
            }, $client->auth_order);
            $order[] = '_DISABLED';
            $order[] = 'login_email';
            $order[] = 'code';
            $client->auth_order = $order;
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
        App\Client::get()->each(function($client)  {
            $order = array_filter($client->auth_order, function($item) {
                return !in_array($item, [
                    '_DISABLED',
                    'login_email',
                    'code'
                ]);
            });
            $order = array_map(function($item) {
                if($item == '_HIDDEN') {
                    return '_';
                } else if($item == 'login_email_code') {
                    return 'login';
                }
                return $item;
            }, $order);
            $client->auth_order = $order;
            $client->save();
        });
    }
}
