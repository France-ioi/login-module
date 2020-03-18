<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LtiConnectionContentId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lti_connections', function (Blueprint $table) {
            $table->string('lti_content_id')->nullable()->after('lti_consumer_key');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('lti_connections', function (Blueprint $table) {
            $table->dropColumn('lti_content_id');
        });
    }
}
