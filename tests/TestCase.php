<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    // seeded by database/seeders/UsersSeeder.php
    const TEST_PASSWORD = '123123';
}
