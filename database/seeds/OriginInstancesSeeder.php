<?php

use Illuminate\Database\Seeder;

class OriginInstancesSeeder extends Seeder
{

    public function run()
    {
        \App\OriginInstance::create([
            'id' => 1,
            'name' => 'Test login module instance (merge)'
        ]);
    }

}
