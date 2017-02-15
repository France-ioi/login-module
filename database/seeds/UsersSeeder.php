<?php

use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $pwd = \Hash::make('123123');

        // admin user
        $user = \App\User::create([
            'login' => 'admin',
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 'admin@admin.admin',
            'role' => 'primary'
        ]));

        // user with email/pwd
        $user = \App\User::create([
            'password' => $pwd
        ]);
        $user->emails()->save(new \App\Email([
            'email' => 't1@t.t',
            'role' => 'primary'
        ]));
        $user->emails()->save(new \App\Email([
            'email' => 't2@t.t',
            'role' => 'secondary'
        ]));


        // user with login/pwd
        $user = \App\User::create([
            'login' => 'test',
            'password' => $pwd
        ]);

    }
}
