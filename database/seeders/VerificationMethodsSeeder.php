<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\VerificationMethod;

class VerificationMethodsSeeder extends Seeder
{
    public function run()
    {
        VerificationMethod::create([
            'name' => 'email_code',
            'user_attributes' => ['primary_email', 'secondary_email']
        ]);
        VerificationMethod::create([
            'name' => 'email_domain',
            'user_attributes' => ['role']
        ]);
        VerificationMethod::create([
            'name' => 'id_upload',
            'user_attributes' => ['first_name', 'last_name', 'birthday', 'country_code']
        ]);
        VerificationMethod::create([
            'name' => 'classroom_upload',
            'user_attributes' => ['role']
        ]);
        VerificationMethod::create([
            'name' => 'peer',
            'user_attributes' => ['role']
        ]);
        VerificationMethod::create([
            'name' => 'imported_data',
            'user_attributes' => ['role'],
            //'public' => false
        ]);
        VerificationMethod::create([
            'name' => 'user_helper',
            'user_attributes' => ['first_name','last_name','graduation_grade','graduation_year','country_code','role','primary_email','secondary_email','student_id','birthday'],
            //'public' => false
        ]);
    }
}