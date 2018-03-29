<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verifications', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->integer('method_id')->unsigned()->index();
            $table->foreign('method_id')->references('id')->on('verification_methods')->onDelete('cascade');
            $table->text('user_attributes');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->datetime('approved_at')->nullable();
            $table->integer('confidence')->nullable();
            $table->text('data')->nullable();
            $table->text('file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verifications');
    }
}
