<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOauthClientVerificationMethodPivotTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth_client_verification_method', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->index();
            $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade');
            $table->integer('verification_method_id')->unsigned()->index();
            $table->foreign('verification_method_id')->references('id')->on('verification_methods')->onDelete('cascade');
            $table->primary(['client_id', 'verification_method_id'], 'oauth_client_verification_method_primary');
            $table->integer('expiration')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('oauth_client_verification_method');
    }
}
