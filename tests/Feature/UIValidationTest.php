<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Block;
use App\Models\BlockInspection;
use App\Models\WorkOrder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UIValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_login_page_contains_required_elements()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('Sign In');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Forgot password?');
        $response->assertSee('PROMAN');
    }

    public function test_dashboard_page_contains_required_elements()
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

    public function test_users_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('List Users');
        $response->assertSee('Search');
        $response->assertSee('Name');
        $response->assertSee('Email');
    }

    public function test_users_create_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/users/create');

        $response->assertStatus(200);
        $response->assertSee('Create User');
        $response->assertSee('Name');
        $response->assertSee('Email');
        $response->assertSee('Password');
        $response->assertSee('Confirm Password');
        $response->assertSee('User Type');
        $response->assertSee('Create User');
        $response->assertSee('Cancel');
        $response->assertSee('input[name="name"]', false);
        $response->assertSee('input[name="email"]', false);
        $response->assertSee('input[name="password"]', false);
        $response->assertSee('input[name="password_confirmation"]', false);
        $response->assertSee('select[name="user_type_id"]', false);
    }

    public function test_block_inspections_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections');

        $response->assertStatus(200);
        $response->assertSee('Block Inspections List');
        $response->assertSee('Schedule Inspection');
        $response->assertSee('Search');
        $response->assertSee('Status');
        $response->assertSee('Date From');
        $response->assertSee('Date To');
        $response->assertSee('Reference');
        $response->assertSee('Block');
        $response->assertSee('Scheduled Date');
        $response->assertSee('Lead Inspector');
        $response->assertSee('Actions');
    }

    public function test_block_inspections_create_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections/create');

        $response->assertStatus(200);
        $response->assertSee('Schedule Block Inspection');
        $response->assertSee('Block');
        $response->assertSee('Scheduled Date Time');
        $response->assertSee('Notes');
        $response->assertSee('Team Members');
        $response->assertSee('Lead Inspector');
        $response->assertSee('Schedule Inspection');
        $response->assertSee('Cancel');
        $response->assertSee('select[name="block_id"]', false);
        $response->assertSee('input[name="scheduled_date_time"]', false);
        $response->assertSee('textarea[name="notes"]', false);
        $response->assertSee('select[name="lead_inspector"]', false);
    }

    public function test_work_orders_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/work-orders');

        $response->assertStatus(200);
        $response->assertSee('Work Orders');
        $response->assertSee('Search');
        $response->assertSee('Code');
        $response->assertSee('Actions');
    }

    public function test_blocks_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/blocks');

        $response->assertStatus(200);
        $response->assertSee('Blocks List');
        $response->assertSee('Search');
        $response->assertSee('Add New Block');
        $response->assertSee('Export');
        $response->assertSee('Name');
        $response->assertSee('Type');
        $response->assertSee('Management Company');
        $response->assertSee('Block Manager');
        $response->assertSee('Address');
        $response->assertSee('Units');
        $response->assertSee('Issues');
        $response->assertSee('Work Orders');
        $response->assertSee('Actions');
    }

    public function test_work_orders_create_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/work-orders/create');

        $response->assertStatus(200);
        $response->assertSee('Create Work Order');
        $response->assertSee('Code');
        $response->assertSee('Property ID');
        $response->assertSee('Contractor ID');
        $response->assertSee('User');
        $response->assertSee('Priority');
        $response->assertSee('Fault Description');
        $response->assertSee('Issue Category');
        $response->assertSee('Issue Type');
        $response->assertSee('Issued Date');
        $response->assertSee('Deadline');
        $response->assertSee('Pricing');
        $response->assertSee('Contact Name');
        $response->assertSee('Contact Number');
        $response->assertSee('Contact Email');
        $response->assertSee('Create Work Order');
        $response->assertSee('Cancel');
    }


    public function test_blocks_create_page_contains_required_elements()
    {
        $response = $this->actingAs($this->admin)->get('/blocks/create');

        $response->assertStatus(200);
        $response->assertSee('Create Block');
        $response->assertSee('Name');
        $response->assertSee('Type');
        $response->assertSee('Location');
        $response->assertSee('Address');
        $response->assertSee('City');
        $response->assertSee('State');
        $response->assertSee('Country');
        $response->assertSee('Zip Code');
        $response->assertSee('Create Block');
        $response->assertSee('Cancel');
    }

    public function test_navigation_menu_contains_required_links()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('href="/users"', false);
        $response->assertSee('href="/blocks"', false);
        $response->assertSee('href="/block-inspections"', false);
        $response->assertSee('href="/work-orders"', false);
        $response->assertSee('href="/block-issues"', false);
        $response->assertSee('href="/block-visits"', false);
    }

    public function test_export_buttons_are_present()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections');

        $response->assertStatus(200);
        $response->assertSee('Export to PDF');
        $response->assertSee('Export to Excel');
        $response->assertSee('Print');
        $response->assertSee('href*="export.pdf"', false);
        $response->assertSee('href*="export.excel"', false);
        $response->assertSee('href*="export.print"', false);
    }

    public function test_pagination_controls_are_present()
    {
        // Create enough data to trigger pagination
        BlockInspection::factory()->count(25)->create();

        $response = $this->actingAs($this->admin)->get('/block-inspections');

        $response->assertStatus(200);
        $response->assertSee('pagination', false);
        $response->assertSee('Next');
        $response->assertSee('Previous');
    }

    public function test_status_filters_are_present()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections');

        $response->assertStatus(200);
        $response->assertSee('Scheduled');
        $response->assertSee('In Progress');
        $response->assertSee('Completed');
        $response->assertSee('Cancelled');
        $response->assertSee('On Hold');
    }

    public function test_form_validation_messages_are_displayed()
    {
        $response = $this->actingAs($this->admin)->post('/users', []);

        $response->assertSessionHasErrors(['name', 'email', 'password', 'user_type_id']);
    }

    public function test_success_messages_are_displayed()
    {
        $userType = \App\Models\UserType::factory()->create();

        $response = $this->actingAs($this->admin)->post('/users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type_id' => $userType->id
        ]);

        $response->assertSessionHas('success', 'User created successfully');
    }

    public function test_modal_dialogs_are_present()
    {
        $response = $this->actingAs($this->admin)->get('/users');

        $response->assertStatus(200);
        $response->assertSee('modal', false);
        $response->assertSee('data-bs-toggle="modal"', false);
    }

    public function test_data_tables_are_initialized()
    {
        $response = $this->actingAs($this->admin)->get('/block-inspections');

        $response->assertStatus(200);
        $response->assertSee('DataTable', false);
        $response->assertSee('table', false);
        $response->assertSee('thead', false);
        $response->assertSee('tbody', false);
    }

    public function test_responsive_design_elements()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('viewport', false);
        $response->assertSee('responsive', false);
        $response->assertSee('col-', false);
        $response->assertSee('row', false);
    }

    public function test_css_framework_classes_are_present()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('bootstrap', false);
        $response->assertSee('btn', false);
        $response->assertSee('form-control', false);
        $response->assertSee('card', false);
        $response->assertSee('navbar', false);
    }

    public function test_javascript_libraries_are_loaded()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('jquery', false);
        $response->assertSee('bootstrap', false);
        $response->assertSee('datatables', false);
    }

    public function test_meta_tags_are_present()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('meta name="description"', false);
        $response->assertSee('meta name="author"', false);
        $response->assertSee('meta name="viewport"', false);
        $response->assertSee('meta name="csrf-token"', false);
    }

    public function test_favicon_is_present()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('favicon', false);
        $response->assertSee('rel="shortcut icon"', false);
    }

    public function test_page_titles_are_correct()
    {
        $response = $this->actingAs($this->admin)->get('/');

        $response->assertStatus(200);
        $response->assertSee('Dashboard | PROMAN', false);

        $response = $this->actingAs($this->admin)->get('/users');
        $response->assertSee('Users | PROMAN', false);

        $response = $this->actingAs($this->admin)->get('/block-inspections');
        $response->assertSee('Block Inspections | PROMAN', false);
    }
}
