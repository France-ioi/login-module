<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Email;
use App\Notifications\EmailVerificationNotification;

class EmailVerificationCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('email_verification_tokens');
        Schema::table('emails', function (Blueprint $table) {
            $table->string('code', 10)->nullable();
        });
        Email::where('verified', false)->chunk(200, function($emails) {
            foreach($emails as $email) {
                $email->code = str_random(10);
                $email->save();
                $email->notify(new EmailVerificationNotification());
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('code');
        });
        Schema::create('email_verification_tokens', function (Blueprint $table) {
            $table->string('token', 40)->primary();
            $table->string('email')->unique();
            $table->timestamps();
        });
    }
}
