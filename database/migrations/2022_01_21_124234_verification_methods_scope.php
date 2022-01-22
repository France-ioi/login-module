<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\VerificationMethod;

class VerificationMethodsScope extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_methods', function (Blueprint $table) {
            $table->boolean('global')->default(false);
        });

        VerificationMethod::where('name', '=', 'email_code')->update([
            'global' => true
        ]);

        Schema::table('verifications', function (Blueprint $table) {
            $table->integer('client_id')->unsigned()->nullable();
            $table->foreign('client_id')->references('id')->on('oauth_clients')->onDelete('cascade')->onUpdate('cascade');     
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verification_methods', function (Blueprint $table) {
            $table->dropColumn('global');
        });

        Schema::table('verifications', function (Blueprint $table) {
            $table->dropForeign('verifications_client_id_foreign');
            $table->dropColumn('client_id');
        });            
    }
}
