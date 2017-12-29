<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ClientBadgeRequired extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->boolean('badge_required')->default(false)->after('badge_url');
        });
        Schema::table('badges', function (Blueprint $table) {
            $table->boolean('override_profile')->default(false);
            $table->text('comments')->nullable();
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
            $table->dropColumn('badge_required');
        });
        Schema::table('badges', function (Blueprint $table) {
            $table->dropColumn('override_profile');
            $table->dropColumn('comments');
        });
    }
}
