<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Block;
use App\Models\BlockInspection;
use App\Models\WorkOrder;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class DashboardTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_dashboard_displays_correctly()
    {
        // Create test data
        $block = Block::factory()->create(['name' => 'Test Block']);
        $inspection = BlockInspection::factory()->create(['block_id' => $block->id]);
        $workOrder = WorkOrder::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    ->assertSee('Welcome')
                    ->assertSee($this->admin->name)
                    
                    // Check dashboard sections are present
                    ->assertSee('Statistics')
                    ->assertSee('Quick Actions')
                    ->assertSee('Recent Activity')
                    
                    // Check navigation elements
                    ->assertSee('Users')
                    ->assertSee('Blocks')
                    ->assertSee('Block Inspections')
                    ->assertSee('Work Orders');
        });
    }

    public function test_dashboard_statistics_cards_display()
    {
        // Create test data for statistics
        $block = Block::factory()->create();
        $inspection = BlockInspection::factory()->create(['block_id' => $block->id]);
        $workOrder = WorkOrder::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Check if statistics cards are present
                    ->assertPresent('.card')
                    ->assertSee('Total')
                    ->assertSee('Active')
                    ->assertSee('Completed');
        });
    }

    public function test_dashboard_quick_actions_work()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test quick action buttons
                    ->assertSee('Add User')
                    ->assertSee('Add Block')
                    ->assertSee('Schedule Inspection')
                    ->assertSee('Create Work Order')
                    
                    // Test quick action links work
                    ->clickLink('Add User')
                    ->assertPathIs('/users/create')
                    ->back()
                    
                    ->clickLink('Add Block')
                    ->assertPathIs('/blocks/create')
                    ->back()
                    
                    ->clickLink('Schedule Inspection')
                    ->assertPathIs('/block-inspections/create')
                    ->back()
                    
                    ->clickLink('Create Work Order')
                    ->assertPathIs('/work-orders/create');
        });
    }

    public function test_dashboard_recent_activity_section()
    {
        // Create recent activity data
        $block = Block::factory()->create(['name' => 'Recent Block']);
        $inspection = BlockInspection::factory()->create(['block_id' => $block->id]);
        $workOrder = WorkOrder::factory()->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Check recent activity section
                    ->assertSee('Recent Activity')
                    ->assertSee('Recent Blocks')
                    ->assertSee('Recent Inspections')
                    ->assertSee('Recent Work Orders');
        });
    }

    public function test_dashboard_responsive_design()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test desktop view
                    ->resize(1920, 1080)
                    ->assertSee('Dashboard')
                    ->assertSee('Statistics')
                    
                    // Test tablet view
                    ->resize(768, 1024)
                    ->assertSee('Dashboard')
                    ->assertSee('Statistics')
                    
                    // Test mobile view
                    ->resize(375, 667)
                    ->assertSee('Dashboard')
                    ->assertSee('Statistics');
        });
    }

    public function test_dashboard_navigation_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test sidebar navigation
                    ->clickLink('Users')
                    ->assertPathIs('/users')
                    ->assertSee('Users List')
                    ->back()
                    
                    ->clickLink('Blocks')
                    ->assertPathIs('/blocks')
                    ->assertSee('Blocks List')
                    ->back()
                    
                    ->clickLink('Block Inspections')
                    ->assertPathIs('/block-inspections')
                    ->assertSee('Block Inspections List')
                    ->back()
                    
                    ->clickLink('Work Orders')
                    ->assertPathIs('/work-orders')
                    ->assertSee('Work Orders List')
                    ->back()
                    
                    ->assertPathIs('/')
                    ->assertSee('Dashboard');
        });
    }

    public function test_dashboard_search_functionality()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test header search
                    ->assertPresent('input[placeholder="Search..."]')
                    ->type('input[placeholder="Search..."]', 'test')
                    ->pause(500)
                    ->assertSee('Recent Searches')
                    ->assertSee('Pages')
                    ->assertSee('Members');
        });
    }

    public function test_dashboard_notifications_dropdown()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test notifications dropdown
                    ->click('@notifications-dropdown')
                    ->assertSee('Notifications')
                    ->assertSee('All Caught Up!')
                    ->assertSee('No pending notifications at the moment');
        });
    }

    public function test_dashboard_issues_dropdown()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test issues dropdown
                    ->click('@issues-dropdown')
                    ->assertSee('Active Issues')
                    ->assertSee('No Active Issues!')
                    ->assertSee('All issues have been resolved');
        });
    }

    public function test_dashboard_user_profile_dropdown()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test user profile dropdown
                    ->click('@user-dropdown')
                    ->assertSee('Welcome ' . $this->admin->name)
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

    public function test_dashboard_theme_switcher()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test theme switcher
                    ->click('@theme-switcher')
                    ->assertSee('Default (light mode)')
                    ->assertSee('Dark')
                    ->assertSee('Auto (system default)');
        });
    }

    public function test_dashboard_fullscreen_toggle()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test fullscreen toggle button is present
                    ->assertPresent('@fullscreen-toggle');
        });
    }

    public function test_dashboard_language_switcher()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Test language switcher
                    ->click('@language-switcher')
                    ->assertSee('English')
                    ->assertSee('Española')
                    ->assertSee('Deutsche')
                    ->assertSee('Italiana')
                    ->assertSee('русский')
                    ->assertSee('中国人')
                    ->assertSee('français')
                    ->assertSee('عربي');
        });
    }

    public function test_dashboard_breadcrumb_navigation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Check breadcrumb is present
                    ->assertSee('Dashboard')
                    ->assertPresent('.breadcrumb');
        });
    }

    public function test_dashboard_page_title()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertTitle('Dashboard | PROMAN - Property Management System');
        });
    }

    public function test_dashboard_favicon_loads()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Check favicon is present
                    ->assertPresent('link[rel="shortcut icon"]');
        });
    }

    public function test_dashboard_meta_tags()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/')
                    ->assertSee('Dashboard')
                    
                    // Check meta tags are present
                    ->assertPresent('meta[name="description"]')
                    ->assertPresent('meta[name="author"]')
                    ->assertPresent('meta[name="viewport"]');
        });
    }
}
