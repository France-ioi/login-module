<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertAdmins extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Artisan::call('db:seed', array('--class' => 'RolesAndPermissionsSeeder'));

        App\User::where('admin', 1)->get()->each(function($user) {
            if($user->hasRole('admin')) return;
            $user->assignRole('admin');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('admin');
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
            $table->boolean('admin')->default(false);
        });
        App\User::role('admin')->get()->each(function($user) {
            $user->admin = true;
            $user->save();
        });
    }
}
