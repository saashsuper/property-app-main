<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class AuthenticationTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function test_user_can_login_successfully()
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@proman.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->assertSee('Sign In')
                    ->type('email', $user->email)
                    ->type('password', 'password123')
                    ->press('Sign In')
                    ->assertPathIs('/')
                    ->assertSee('Dashboard')
                    ->assertSee($user->name);
        });
    }

    public function test_user_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@proman.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                    ->assertSee('Sign In')
                    ->type('email', $user->email)
                    ->type('password', 'wrongpassword')
                    ->press('Sign In')
                    ->assertPathIs('/login')
                    ->assertSee('These credentials do not match our records');
        });
    }

    public function test_user_can_logout()
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@proman.com',
            'password' => bcrypt('password123')
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    ->click('@user-dropdown')
                    ->clickLink('Logout')
                    ->assertPathIs('/login')
                    ->assertSee('Sign In');
        });
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    ->assertSee('Welcome')
                    ->assertSee($user->name);
        });
    }

    public function test_unauthenticated_user_redirected_to_login()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertPathIs('/login')
                    ->assertSee('Sign In');
        });
    }

    public function test_user_can_navigate_to_different_sections()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test navigation to Users
                    ->clickLink('Users')
                    ->assertPathIs('/users')
                    ->assertSee('Users List')
                    
                    // Test navigation to Blocks
                    ->clickLink('Blocks')
                    ->assertPathIs('/blocks')
                    ->assertSee('Blocks List')
                    
                    // Test navigation to Block Inspections
                    ->clickLink('Block Inspections')
                    ->assertPathIs('/block-inspections')
                    ->assertSee('Block Inspections List')
                    
                    // Test navigation back to Dashboard
                    ->clickLink('Dashboard')
                    ->assertPathIs('/')
                    ->assertSee('Dashboard');
        });
    }

    public function test_sidebar_navigation_works_correctly()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test sidebar menu items are visible
                    ->assertSee('Users')
                    ->assertSee('Blocks')
                    ->assertSee('Block Inspections')
                    ->assertSee('Work Orders')
                    ->assertSee('Issues')
                    
                    // Test that sidebar is responsive
                    ->resize(768, 1024) // Tablet size
                    ->assertSee('Dashboard')
                    ->resize(1920, 1080) // Desktop size
                    ->assertSee('Dashboard');
        });
    }

    public function test_user_profile_dropdown_works()
    {
        $user = User::factory()->admin()->create([
            'name' => 'Test Admin',
            'email' => 'admin@proman.com'
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Click on user profile dropdown
                    ->click('@user-dropdown')
                    ->assertSee('Welcome ' . $user->name)
                    ->assertSee('Profile')
                    ->assertSee('Messages')
                    ->assertSee('Taskboard')
                    ->assertSee('Help')
                    ->assertSee('Role : Admin')
                    ->assertSee('Profile Settings')
                    ->assertSee('Lock Screen')
                    ->assertSee('Logout');
        });
    }

    public function test_search_functionality_in_header()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test search input is present
                    ->assertPresent('input[placeholder="Search..."]')
                    
                    // Test search dropdown appears
                    ->type('input[placeholder="Search..."]', 'test')
                    ->pause(500) // Wait for search results
                    ->assertSee('Recent Searches')
                    ->assertSee('Pages')
                    ->assertSee('Members');
        });
    }

    public function test_notification_dropdown_works()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Click on notifications dropdown
                    ->click('@notifications-dropdown')
                    ->assertSee('Notifications')
                    ->assertSee('All Caught Up!')
                    ->assertSee('No pending notifications at the moment');
        });
    }

    public function test_issues_dropdown_works()
    {
        $user = User::factory()->admin()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Click on issues dropdown
                    ->click('@issues-dropdown')
                    ->assertSee('Active Issues')
                    ->assertSee('No Active Issues!')
                    ->assertSee('All issues have been resolved');
        });
    }
}
