<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class HideDomainVerificationMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('verification_methods')->where('name', 'email_domain')->update(['public' => 0]);
        DB::table('verification_methods')->where('name', 'id_upload')->update(['global' => 1]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('verification_methods')->where('name', 'email_domain')->update(['public' => 1]);
        DB::table('verification_methods')->where('name', 'id_upload')->update(['global' => 0]);
    }
}
