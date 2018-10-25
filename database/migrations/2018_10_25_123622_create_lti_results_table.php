<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLtiResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lti_results', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('lti_connection_id')->unsigned()->index();
            $table->foreign('lti_connection_id')->references('id')->on('lti_connections')->onDelete('cascade')->onUpdate('cascade');
            $table->float('score');
            $table->integer('attempts');
            $table->dateTime('last_attempt');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lti_results');
    }
}
