<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Passport 4 -> 12: columns added to oauth_clients by later Passport versions
 * (provider since 9.x, nullable secret for public/PKCE clients since 9.x).
 */
class UpgradePassportTables extends Migration
{
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('provider')->nullable()->after('secret');
            $table->string('secret', 100)->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn('provider');
            $table->string('secret', 100)->nullable(false)->change();
        });
    }
}
