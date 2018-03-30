<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VerificationsData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE verifications CHANGE `data` `message` TEXT');
        Schema::table('verifications', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->text('rejected_attributes');

        });
        App\Verification::get()->each(function($verification) {
            $verification->rejected_attributes = [];
            $verification->save();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE verifications CHANGE `message` `data` TEXT');
        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('rejected_attributes');
        });
    }
}
