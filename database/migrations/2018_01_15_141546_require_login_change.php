<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RequireLoginChange extends Migration
{


    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('login_change_required')->after('login_revalidate_required')->default(false);
        });

        App\User::whereNotNull('login')->chunk(200, function($users) {
            foreach($users as $user) {
                if(preg_match(config('profile.login_validator.existing'), $user->login) !== 1) {
                    $user->login_change_required = true;
                    $user->save();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('login_change_required');
        });
    }
}
