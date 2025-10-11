<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Force test database connection for all tests
        config(['database.connections.mysql.database' => 'proman_test']);
        \DB::purge('mysql');
        \DB::reconnect('mysql');
    }
}
