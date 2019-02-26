<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class VertificationMethodVisibility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_methods', function (Blueprint $table) {
            $table->boolean('public')->default(true);
        });
        \DB::table('verification_methods')->where('name', 'user_helper')->orWhere('name', 'imported_data')->update(['public' => false]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verification_methods', function (Blueprint $table) {
            $table->dropColumn('public');
        });
    }
}
