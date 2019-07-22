<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UnsignedIds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_access_tokens', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->change();
            $table->integer('client_id')->unsigned()->change();
        });
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->nullable()->change();
        });
        Schema::table('oauth_personal_access_clients', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
