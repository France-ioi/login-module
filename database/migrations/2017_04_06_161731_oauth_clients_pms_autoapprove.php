<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class OauthClientsPmsAutoapprove extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->boolean('pms_autoapprove')->default(false);
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
            $table->dropColumn('pms_autoapprove');
        });
    }
}
