<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\LoginModule\AuthList;

class AuthOrder extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $auth_list = new App\LoginModule\AuthList;
        App\Client::get()->each(function($client) use ($auth_list) {
            $client->auth_order = array_merge(array_values($client->auth_order), ['_']);
            $client->auth_order = $auth_list->normalize($client->auth_order);
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
        $auth_list = new App\LoginModule\AuthList;
        App\Client::get()->each(function($client) use ($auth_list) {
            $methods = $auth_list->split($client->auth_order);
            $client->auth_order = $methods['visible'];
            $client->save();
        });
    }
}
