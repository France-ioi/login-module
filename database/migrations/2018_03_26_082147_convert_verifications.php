<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ConvertVerifications extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // convert teacher_verified
        $method = App\VerificationMethod::where('name', 'imported_data')->first();
        App\User::where('teacher_verified', true)->chunk(
            100,
            function($users) use ($method) {
                foreach($users as $user) {
                    $verification = new App\Verification([
                        'user_attributes' => ['role'],
                        'status' => 'approved',
                        'method_id' => $method->id
                    ]);
                    $user->verifications()->save($verification);
                }
            }
        );
        $method = null;
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('teacher_verified');
        });

        // convert emails verification
        $method_code = App\VerificationMethod::where('name', 'email_code')->first();
        $method_domain = App\VerificationMethod::where('name', 'email_domain')->first();
        App\Email::with('user')->chunk(
            100,
            function($emails) use ($method_code, $method_domain) {
                foreach($emails as $email) {
                    if($email->verified) {
                        $verification = new App\Verification([
                            'user_attributes' => [$email->role.'_email'],
                            'status' => 'approved',
                            'method_id' => $method_code->id
                        ]);
                        $email->user->verifications()->save($verification);
                    }

                    if($email->user->role == 'teacher' &&
                        App\LoginModule\TeacherDomain::verify($email->user)) {
                        $verification = new App\Verification([
                            'user_attributes' => ['role'],
                            'status' => 'approved',
                            'method_id' => $method_domain->id
                        ]);
                        $email->user->verifications()->save($verification);
                    }
                }
            }
        );
        Schema::table('emails', function (Blueprint $table) {
            $table->dropColumn('verified');
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
            $table->boolean('teacher_verified')->default(false);
        });
        $method = App\VerificationMethod::where('name', 'imported_data')->first();
        App\Verification::where('method_id', $method->id)->where('status', 'approved')->chunk(
            100,
            function($verifications) {
                foreach($verifications as $verification) {
                    $user = App\User::find($verification->user_id);
                    if(!$user) continue;
                    $user->teacher_verified = true;
                    $user->save();
                }
            }
        );


        Schema::table('emails', function (Blueprint $table) {
            $table->boolean('verified')->default(false);
        });

        $method = App\VerificationMethod::where('name', 'email_code')->first();
        App\Verification::where('method_id', $method->id)->where('status', 'approved')->chunk(
            100,
            function($verifications) use ($method) {
                foreach($verifications as $verification) {
                    $user = App\User::find($verification->user_id);
                    if(!$user) continue;

                    $role = str_replace('_email', '', $verification->user_attributes[0]);
                    $email = $user->emails()->where('role', $role)->first();
                    if(!$email) continue;

                    $email->verified = true;
                    $email->save();
                }
            }
        );
    }
}
