<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class MergeLmAccounts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('origin_instances', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->string('name');
        });
        Schema::table('users', function (Blueprint $table) {
            $table->integer('merge_group_id')->nullable()->unsigned()->index();
            $table->integer('origin_instance_id')->nullable()->unsigned()->index();
            $table->foreign('origin_instance_id')->references('id')->on('origin_instances')->onDelete('cascade')->onUpdate('cascade');
            $table->dropUnique('users_login_unique');
            $table->unique(['login', 'origin_instance_id']);
            $table->boolean('login_revalidate_required')->default(false)->after('login');
        });
        Schema::table('emails', function (Blueprint $table) {
            $table->integer('origin_instance_id')->nullable()->unsigned()->index();
            $table->foreign('origin_instance_id')->references('id')->on('origin_instances')->onDelete('cascade')->onUpdate('cascade');
            $table->dropUnique('emails_email_unique');
            $table->unique(['email', 'origin_instance_id']);
            $table->boolean('email_revalidate_required')->default(false)->after('email');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique('users_login_origin_instance_id_unique');
            $table->dropColumn('merge_group_id');
            $table->dropForeign('users_origin_instance_id_foreign');
            $table->dropColumn('origin_instance_id');
            $table->unique('login');
            $table->dropColumn('login_revalidate_required');
        });
        Schema::table('emails', function (Blueprint $table) {
            $table->dropUnique('emails_email_origin_instance_id_unique');
            $table->unique('email');
            $table->dropForeign('emails_origin_instance_id_foreign');
            $table->dropColumn('origin_instance_id');
            $table->dropColumn('email_revalidate_required');
        });
        Schema::dropIfExists('origin_instances');
    }
}
