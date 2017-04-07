<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfficialDomainsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('official_domains', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('country_code', 2)->index();
            $table->string('domain', 100);
            $table->index('country_code', 'domain');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('official_domains');
    }
}
