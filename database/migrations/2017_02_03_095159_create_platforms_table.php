<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('oauth_client_id', 40);
            $table->foreign('oauth_client_id')->references('id')->on('oauth_clients')->onDelete('cascade')->onUpdate('cascade');
            $table->string('name');
            $table->text('public_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('platforms');
    }
}
