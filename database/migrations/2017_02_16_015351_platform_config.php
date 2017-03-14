<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class PlatformConfig extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('profile_fields')->nallable();
            $table->text('auth_order')->nallable();
            $table->text('public_key')->nallable();
            $table->string('badge_url')->nallable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('profile_fields');
            $table->dropColumn('auth_order');
            $table->dropColumn('public_key');
            $table->dropColumn('badge_url');
        });
    }
}