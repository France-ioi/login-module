<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UserGenderOther extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // because of Doctrine DBAL issue in old versions
        DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` enum('m','f','o') COLLATE 'utf8_unicode_ci' NULL AFTER `picture`");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("UPDATE `users` SET `gender` = NULL WHERE `gender` = 'o'");
        DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` enum('m','f') COLLATE 'utf8_unicode_ci' NULL AFTER `picture`");
    }
}
