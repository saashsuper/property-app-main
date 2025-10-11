<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HomeControllerTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function guest_redirects_to_login()
    {
        $response = $this->get('/');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_root()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act as the user
        $this->actingAs($user);

        // Access root
        $response = $this->get('/');

        // Should redirect to dashboard
        $response->assertRedirect('/dashboard');
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    }

    /** @test */
    public function authenticated_user_can_access_dashboard()
    {
        // Create a user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        // Act as the user
        $this->actingAs($user);

        // Access dashboard
        $response = $this->get('/dashboard');

        $response->assertStatus(200);
    }
}
