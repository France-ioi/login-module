<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //$this->call(RolesAndPermissionsSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(ClientsSeeder::class);
        $this->call(OfficialDomainsSeeder::class);
        $this->call(OriginInstancesSeeder::class);
    }
}
