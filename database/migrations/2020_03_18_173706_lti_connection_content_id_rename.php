<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class LtiConnectionContentIdRename extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('lti_connections', function (Blueprint $table) {
            $table->renameColumn('lti_content_id', 'content_id');
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
            $table->renameColumn('content_id', 'lti_content_id');
        });
    }
}
