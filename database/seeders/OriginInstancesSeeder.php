<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class OriginInstancesSeeder extends Seeder
{

    public function run()
    {
        \App\OriginInstance::create([
            'id' => 1,
            'name' => 'Test login module instance A (merge)'
        ]);
        \App\OriginInstance::create([
            'id' => 2,
            'name' => 'Test login module instance B (merge)'
        ]);
    }

}
