<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Country;

class OfficialDomainsSeeder extends Seeder
{

    public function run()
    {
        $codes = ['RU', 'FR', 'DE'];
        $countries = Country::whereIn('code', $codes)->get()->pluck('id', 'code');
        foreach($countries as $code => $id) {
            \App\OfficialDomain::create([
                'domain' => 'test2.'.strtolower($code),
                'country_id' => $id
            ]);
        }
    }

}
