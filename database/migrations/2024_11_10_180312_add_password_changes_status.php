<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPasswordChangesStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('password_changes', function (Blueprint $table) {
            $table->enum('status', ['done', 'throttled', 'rejected'])->default('done');
            // Add indexes for throttle
            $table->index('created_at');
            $table->index('requester_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('password_changes', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
