<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OfficialDomainsSeeder extends Seeder
{

    public function run()
    {
        $codes = ['RU', 'FR', 'DE'];
        foreach($codes as $code) {
            \App\OfficialDomain::create([
                'domain' => 'test.'.strtolower($code),
                'country_code' => $code
            ]);
        }
    }

}
