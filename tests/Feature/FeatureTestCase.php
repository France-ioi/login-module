<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class FeatureTestCase extends TestCase
{
    use RefreshDatabase;

    // run database/seeders/DatabaseSeeder (admin, test1, test2 users, example clients)
    protected $seed = true;

    protected function user($login)
    {
        return User::where('login', $login)->firstOrFail();
    }
}
