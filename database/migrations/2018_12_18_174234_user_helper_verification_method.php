<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\VerificationMethod;
use App\LoginModule\Profile\Verification\Verification;

class UserHelperVerificationMethod extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        VerificationMethod::create([
            'name' => 'user_helper',
            'user_attributes' => Verification::ATTRIBUTES
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        VerificationMethod::where('name', 'user_helper')->delete();
    }
}
