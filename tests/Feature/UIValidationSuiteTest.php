<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UIValidationSuiteTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    /**
     * Test suite for all UI validation tests
     * Run with: php artisan test tests/Feature/UIValidationSuiteTest.php
     */
    public function test_ui_validation_suite()
    {
        // Test 1: Login Page
        $this->test_login_page_elements();
        
        // Test 2: Dashboard Page
        $this->test_dashboard_page_elements();
        
        // Test 3: Users Page
        $this->test_users_page_elements();
        
        // Test 4: Block Inspections Page
        $this->test_block_inspections_page_elements();
        
        // Test 5: Work Orders Page
        $this->test_work_orders_page_elements();
    }

    private function test_login_page_elements()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Forgot password?');
        $response->assertSee('PROMAN');
    }

    private function test_dashboard_page_elements()
    {
        $response = $this->actingAs($this->admin)->get('/');
        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee('Welcome');
        $response->assertSee($this->admin->name);
        $response->assertSee('Users');
        $response->assertSee('Blocks');
        $response->assertSee('Block Inspections');
        $response->assertSee('Work Orders');
    }

    private function test_users_page_elements()
    {
        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertStatus(200);
        $response->assertSee('List Users');
        $response->assertSee('Search');
        $response->assertSee('Name');
        $response->assertSee('Email');
    }

    private function test_block_inspections_page_elements()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections');
        $response->assertStatus(200);
        $response->assertSee('Block Inspections List');
        $response->assertSee('Search');
        $response->assertSee('Status');
        $response->assertSee('Reference');
        $response->assertSee('Block');
        $response->assertSee('Scheduled Date');
        $response->assertSee('Lead Inspector');
        $response->assertSee('Actions');
    }

    private function test_work_orders_page_elements()
    {
        $response = $this->actingAs($this->admin)->get('/work-orders');
        $response->assertStatus(200);
        $response->assertSee('Work Orders');
        $response->assertSee('Search');
        $response->assertSee('Code');
        $response->assertSee('Actions');
    }
}
