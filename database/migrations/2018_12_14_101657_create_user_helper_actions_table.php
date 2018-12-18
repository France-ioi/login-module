<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserHelperActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_helper_actions', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('target_user_id')->unsigned()->nullable();
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('type', ['search', 'change', 'password', 'verification']);
            $table->string('hash', 32);
            $table->index(['user_id', 'created_at']);
            $table->text('details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_helper_actions');
    }
}
