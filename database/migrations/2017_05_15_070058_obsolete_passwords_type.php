<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ObsoletePasswordsType extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('obsolete_passwords', function (Blueprint $table) {
            $table->string('type')->default('md5');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('obsolete_passwords', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
