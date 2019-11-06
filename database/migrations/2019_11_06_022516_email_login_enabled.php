<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class EmailLoginEnabled extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropUnique('emails_email_origin_instance_id_unique');
            $table->boolean('login_enabled')->default(true)->after('email_revalidate_required');
            $table->unique(['email', 'login_enabled', 'origin_instance_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropUnique('emails_email_login_enabled_origin_instance_id_unique');
            $table->dropColumn('login_enabled');
            $table->unique(['email', 'origin_instance_id']);
        });
    }
}
