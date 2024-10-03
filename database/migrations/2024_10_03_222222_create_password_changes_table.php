<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePasswordChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('password_changes', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('requester_user_id')->unsigned();
            $table->integer('target_user_id')->unsigned();
            // No foreign key constraints, because each user might be deleted
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('password_changes');
    }
}
