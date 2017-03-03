<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('login')->nullable()->unique();
            $table->string('password')->nullable();
            $table->boolean('admin')->default(false);
            $table->string('language', 2)->nullable();

            $table->string('first_name', 100)->nullable();
            $table->string('last_name', 100)->nullable();
            $table->string('country_code', 3)->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('primary_phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->enum('role', ['student', 'teacher', 'other'])->nullable();
            $table->string('school_grade')->nullable();
            $table->string('student_id')->nullable();
            $table->string('ministry_of_education')->nullable();
            $table->boolean('ministry_of_education_fr')->default(false);
            $table->date('birthday')->nullable();
            $table->text('presentation')->nullable();
            $table->text('website')->nullable();
            $table->string('ip', 16);
            $table->string('picture')->nullable();
            $table->enum('gender', ['m', 'f'])->nullable();
            $table->integer('graduation_year')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->dateTime('last_login');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
