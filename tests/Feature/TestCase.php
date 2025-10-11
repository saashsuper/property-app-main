<?php

namespace Tests\Feature;

use Tests\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\DatabaseTransactions;

abstract class TestCase extends BaseTestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Run migrations for testing
        $this->artisan('migrate', ['--env' => 'testing']);
    }
}
