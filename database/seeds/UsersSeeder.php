<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{

    public function run()
    {
        \DB::table('users')->delete();
        $pwd_hash = Hash::make('123123');
        $user = \App\User::create([
            'name' => 'test',
            'email' => 'test@test.test',
            'password' => $pwd_hash
        ]);


        $profle = new \App\Profile([
            'first_name' => 'first name',
            'last_name' => 'last name',
            'address' => 'address',
            'zipcode' => 'zipcode',
            'city' => 'city'
        ]);
        $user->profile()->save($profle);
    }
}
