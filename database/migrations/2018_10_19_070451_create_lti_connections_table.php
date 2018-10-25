<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtiConnectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_connections', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade')->onUpdate('cascade');
            $table->string('lti_consumer_key');
            $table->string('lti_context_id');
            $table->string('lti_user_id')->index();
            $table->unique(['lti_user_id', 'lti_context_id', 'lti_consumer_key'], 'lti_index_unique');
            $table->index(['lti_user_id', 'lti_consumer_key'], 'lti_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti_connections');
    }
}
