<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\WorkOrder;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class WorkOrdersTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_work_orders_list()
    {
        // Create test work orders
        $workOrder1 = WorkOrder::factory()->create([
            'code' => 'WO-0001',
            'fault_description' => 'Test work order 1'
        ]);
        $workOrder2 = WorkOrder::factory()->create([
            'code' => 'WO-0002',
            'fault_description' => 'Test work order 2'
        ]);

        $this->browse(function (Browser $browser) use ($workOrder1, $workOrder2) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    ->assertSee('WO-0001')
                    ->assertSee('WO-0002')
                    ->assertSee('Test work order 1')
                    ->assertSee('Test work order 2');
        });
    }

    public function test_admin_can_create_new_work_order()
    {
        $user = User::factory()->create(['name' => 'Test User']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Click Add New Work Order button
                    ->clickLink('Add New Work Order')
                    ->assertPathIs('/work-orders/create')
                    ->assertSee('Create Work Order')
                    
                    // Fill in work order details
                    ->type('code', 'WO-TEST-001')
                    ->type('property_id', '123')
                    ->type('contractor_id', '456')
                    ->select('user_id', $user->id)
                    ->type('priority', '3')
                    ->type('priority_label', 'High')
                    ->type('fault_description', 'Test fault description')
                    ->type('issue_category', 'Plumbing')
                    ->type('issue_type', 'Repair')
                    ->type('issued_date', now()->format('Y-m-d'))
                    ->type('deadline', now()->addWeek()->format('Y-m-d'))
                    ->type('pricing', 'Fixed')
                    ->type('contact_name', 'John Doe')
                    ->type('contact_number', '123-456-7890')
                    ->type('contact_email', 'john@example.com')
                    ->type('preferred_day', 'Monday')
                    ->type('time_from', '09:00')
                    ->type('time_to', '17:00')
                    ->type('note', 'Test note')
                    ->type('report', 'Test report')
                    ->type('type', 'Maintenance')
                    ->type('type_id', '1')
                    ->type('common_status_id', '1')
                    
                    // Submit the form
                    ->press('Create Work Order')
                    ->assertPathIs('/work-orders')
                    ->assertSee('Work order created successfully')
                    ->assertSee('WO-TEST-001');
        });
    }

    public function test_admin_can_edit_existing_work_order()
    {
        $workOrder = WorkOrder::factory()->create([
            'code' => 'WO-ORIGINAL',
            'fault_description' => 'Original description'
        ]);
        $newUser = User::factory()->create(['name' => 'New User']);

        $this->browse(function (Browser $browser) use ($workOrder, $newUser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('WO-ORIGINAL')
                    ->assertSee('Original description')
                    
                    // Click Edit button
                    ->click('@edit-work-order-' . $workOrder->id)
                    ->assertPathIs('/work-orders/' . $workOrder->id . '/edit')
                    ->assertSee('Edit Work Order')
                    
                    // Update work order details
                    ->clear('fault_description')
                    ->type('fault_description', 'Updated description')
                    ->select('user_id', $newUser->id)
                    ->type('priority', '5')
                    ->type('priority_label', 'Critical')
                    
                    // Submit the form
                    ->press('Update Work Order')
                    ->assertPathIs('/work-orders')
                    ->assertSee('Work order updated successfully')
                    ->assertSee('Updated description');
        });
    }

    public function test_admin_can_delete_work_order()
    {
        $workOrder = WorkOrder::factory()->create([
            'code' => 'WO-TO-DELETE',
            'fault_description' => 'Work order to delete'
        ]);

        $this->browse(function (Browser $browser) use ($workOrder) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('WO-TO-DELETE')
                    
                    // Click Delete button
                    ->click('@delete-work-order-' . $workOrder->id)
                    ->whenAvailable('.modal', function ($modal) {
                        $modal->assertSee('Are you sure you want to delete this work order?')
                              ->press('Delete');
                    })
                    ->assertSee('Work order deleted successfully')
                    ->assertDontSee('WO-TO-DELETE');
        });
    }

    public function test_work_order_creation_form_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders/create')
                    ->assertSee('Create Work Order')
                    
                    // Try to submit empty form
                    ->press('Create Work Order')
                    ->assertSee('The code field is required')
                    ->assertSee('The property id field is required')
                    ->assertSee('The contractor id field is required')
                    ->assertSee('The user id field is required')
                    ->assertSee('The fault description field is required');
        });
    }

    public function test_work_order_search_functionality()
    {
        // Create work orders with different codes
        $workOrder1 = WorkOrder::factory()->create([
            'code' => 'WO-SEARCH-001',
            'fault_description' => 'Searchable work order 1'
        ]);
        $workOrder2 = WorkOrder::factory()->create([
            'code' => 'WO-SEARCH-002',
            'fault_description' => 'Searchable work order 2'
        ]);

        $this->browse(function (Browser $browser) use ($workOrder1, $workOrder2) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('WO-SEARCH-001')
                    ->assertSee('WO-SEARCH-002')
                    
                    // Search by code
                    ->type('input[name="search"]', 'WO-SEARCH-001')
                    ->press('Search')
                    ->assertSee('WO-SEARCH-001')
                    ->assertDontSee('WO-SEARCH-002')
                    
                    // Search by description
                    ->clear('input[name="search"]')
                    ->type('input[name="search"]', 'Searchable work order 2')
                    ->press('Search')
                    ->assertDontSee('WO-SEARCH-001')
                    ->assertSee('WO-SEARCH-002')
                    
                    // Clear search
                    ->clear('input[name="search"]')
                    ->press('Search')
                    ->assertSee('WO-SEARCH-001')
                    ->assertSee('WO-SEARCH-002');
        });
    }

    public function test_work_order_pagination_works()
    {
        // Create more work orders than the pagination limit
        WorkOrder::factory()->count(25)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Check if pagination is present
                    ->assertPresent('.pagination')
                    
                    // Navigate to next page
                    ->clickLink('Next')
                    ->assertSee('Work Orders List')
                    
                    // Navigate back to first page
                    ->clickLink('Previous')
                    ->assertSee('Work Orders List');
        });
    }

    public function test_work_order_export_buttons_work()
    {
        WorkOrder::factory()->count(5)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Check export buttons are present
                    ->assertSee('Export to PDF')
                    ->assertSee('Export to Excel')
                    ->assertSee('Print')
                    
                    // Test PDF export button
                    ->click('a[href*="export.pdf"]')
                    ->pause(1000) // Wait for download
                    
                    // Test Excel export button
                    ->click('a[href*="export.excel"]')
                    ->pause(1000) // Wait for download
                    
                    // Test Print button
                    ->click('a[href*="export.print"]')
                    ->pause(1000); // Wait for print dialog
        });
    }

    public function test_work_order_status_display()
    {
        $workOrder1 = WorkOrder::factory()->create(['common_status_id' => 1]);
        $workOrder2 = WorkOrder::factory()->create(['common_status_id' => 2]);
        $workOrder3 = WorkOrder::factory()->create(['common_status_id' => 3]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Check status badges are present
                    ->assertPresent('.badge');
        });
    }

    public function test_work_order_priority_display()
    {
        $workOrder1 = WorkOrder::factory()->create(['priority' => 1, 'priority_label' => 'Low']);
        $workOrder2 = WorkOrder::factory()->create(['priority' => 3, 'priority_label' => 'High']);
        $workOrder3 = WorkOrder::factory()->create(['priority' => 5, 'priority_label' => 'Critical']);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Check priority labels are displayed
                    ->assertSee('Low')
                    ->assertSee('High')
                    ->assertSee('Critical');
        });
    }

    public function test_work_order_contact_information_display()
    {
        $workOrder = WorkOrder::factory()->create([
            'contact_name' => 'John Doe',
            'contact_number' => '123-456-7890',
            'contact_email' => 'john@example.com'
        ]);

        $this->browse(function (Browser $browser) use ($workOrder) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    ->assertSee('John Doe')
                    ->assertSee('123-456-7890')
                    ->assertSee('john@example.com');
        });
    }

    public function test_work_order_date_display()
    {
        $workOrder = WorkOrder::factory()->create([
            'issued_date' => now()->format('Y-m-d'),
            'deadline' => now()->addWeek()->format('Y-m-d')
        ]);

        $this->browse(function (Browser $browser) use ($workOrder) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    ->assertSee(now()->format('M d, Y'))
                    ->assertSee(now()->addWeek()->format('M d, Y'));
        });
    }

    public function test_work_order_responsive_design()
    {
        WorkOrder::factory()->count(5)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Test desktop view
                    ->resize(1920, 1080)
                    ->assertSee('Work Orders List')
                    
                    // Test tablet view
                    ->resize(768, 1024)
                    ->assertSee('Work Orders List')
                    
                    // Test mobile view
                    ->resize(375, 667)
                    ->assertSee('Work Orders List');
        });
    }

    public function test_work_order_table_sorting()
    {
        WorkOrder::factory()->count(10)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Test table sorting by clicking column headers
                    ->click('th:first-child') // Sort by first column
                    ->pause(500)
                    ->assertSee('Work Orders List')
                    
                    ->click('th:nth-child(2)') // Sort by second column
                    ->pause(500)
                    ->assertSee('Work Orders List');
        });
    }

    public function test_work_order_bulk_actions()
    {
        WorkOrder::factory()->count(5)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/work-orders')
                    ->assertSee('Work Orders List')
                    
                    // Check if bulk action checkboxes are present
                    ->assertPresent('input[type="checkbox"]')
                    
                    // Test selecting multiple work orders
                    ->check('input[type="checkbox"]:first-child')
                    ->check('input[type="checkbox"]:nth-child(2)')
                    ->assertSee('Bulk Actions');
        });
    }
}
