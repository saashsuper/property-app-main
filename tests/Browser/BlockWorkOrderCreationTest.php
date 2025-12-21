<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Block;
use App\Models\BlockUnit;
use App\Models\BlockIssue;
use App\Models\BlockBuilding;
use App\Models\ContractCompany;
use App\Models\UserType;
use App\Models\Priority;
use App\Models\IssueStatus;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockWorkOrderCreationTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $block;
    protected $blockUnit;
    protected $blockIssue;
    protected $contractCompany;
    protected $propertyManager;
    protected $priority;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->admin()->create();
        
        // Create priority
        $this->priority = Priority::factory()->normal()->create();
        
        // Create issue status
        $issueStatus = IssueStatus::factory()->open()->create();
        
        // Create block
        $this->block = Block::factory()->create([
            'name' => 'Test Block for Work Orders'
        ]);
        
        // Create building
        $building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'name' => 'Building A'
        ]);
        
        // Create unit
        $this->blockUnit = BlockUnit::factory()->create([
            'block_id' => $this->block->id,
            'block_building_id' => $building->id,
            'unit_code' => '101',
            'unit_name' => 'Unit 101'
        ]);
        
        // Create issue for the unit
        // Note: Factory includes invalid 'priority' and 'status' string fields that don't exist in DB
        // So we create directly with only valid database fields
        $this->blockIssue = BlockIssue::create([
            'ref_no' => 'ISS-TEST-' . uniqid(),
            'block_id' => $this->block->id,
            'block_unit_id' => $this->blockUnit->id,
            'priority_id' => $this->priority->id,
            'issue_status_id' => $issueStatus->id,
            'issue' => 'Test Issue for Work Order Creation',
            'issued_by' => $this->admin->id,
            'reported_by' => $this->admin->id,
            'created_by' => $this->admin->id,
            'updated_by' => $this->admin->id,
            'issued_from' => 1,
            'from_id' => $this->admin->id,
        ]);
        
        // Create contract company for outsource work orders
        $this->contractCompany = ContractCompany::create([
            'company_name' => 'Test Contract Company',
            'address' => '123 Test Street',
            'phone_number' => '123-456-7890',
            'created_by' => $this->admin->id
        ]);
        
        // Create property manager user for inhouse work orders
        $propertyManagerType = UserType::firstOrCreate(
            ['name' => 'Property manager'],
            ['description' => 'Property Manager']
        );
        
        $this->propertyManager = User::factory()->create([
            'name' => 'Test Property Manager',
            'email' => 'property.manager@test.com',
            'user_type_id' => $propertyManagerType->id
        ]);
    }

    public function test_can_create_work_order_from_edit_block_work_order_tab_outsource()
    {
        $this->browse(function (Browser $browser) {
            // Ensure admin has Super Admin role (user type is already Super Admin)
            try {
                $role = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'Super Admin']);
                if (!$this->admin->hasRole('Super Admin')) {
                    $this->admin->assignRole($role);
                }
            } catch (\Exception $e) {
                // Role might already be assigned or doesn't exist - continue
            }
            
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->pause(5000) // Wait longer for page to load
                    // Check if we're on the right path
                    ->assertPathIs('/blocks/' . $this->block->id . '/edit')
                    // Get page source for debugging
                    ->driver->getPageSource()
                    ->pause(2000)
                    // Try to find Work Orders tab by text first, then by ID
                    ->waitForText('Work Orders', 15)
                    ->clickLink('Work Orders')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(1000) // Wait for tab to load
                    ->assertSee('Work Orders')
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500) // Wait for modal to open
                    ->assertSee('Create Work Order')
                    
                    // Fill in work order form
                    // Work Order Type - should default to "outsource"
                    ->assertValue('#work_order_type', 'outsource')
                    
                    // Select Contract Company (for outsource)
                    ->select('#work_order_contract_company_id', $this->contractCompany->id)
                    
                    // Select Priority
                    ->select('#work_order_priority_id', $this->priority->id)
                    
                    // Select Unit
                    ->select('#work_order_unit_id', $this->blockUnit->id)
                    ->pause(1000) // Wait for issues to load
                    
                    // Select Issue (should be populated after unit selection)
                    ->waitFor('#block_issue_id_select:not([disabled])', 5)
                    ->select('#block_issue_id_select', $this->blockIssue->id)
                    
                    // Select Status
                    ->select('#work_order_status', '1') // Pending
                    
                    // Add optional comment
                    ->type('#work_order_comment', 'Test work order comment from browser test')
                    
                    // Submit the form
                    ->press('Create Work Order')
                    ->pause(2000) // Wait for AJAX submission and table refresh
                    
                    // Verify success - work order should appear in the table
                    ->assertSee('Work Orders')
                    ->assertSee($this->blockIssue->ref_no ?? 'Test Issue')
                    ->assertSee('Normal') // Priority label
                    ->assertSee('Pending'); // Status
        });
    }

    public function test_can_create_work_order_from_edit_block_work_order_tab_inhouse()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->assertSee('Edit Block')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(500) // Wait for tab to load
                    ->assertSee('Work Orders')
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500) // Wait for modal to open
                    ->assertSee('Create Work Order')
                    
                    // Change Work Order Type to "inhouse"
                    ->select('#work_order_type', 'inhouse')
                    ->pause(500) // Wait for field toggle
                    
                    // Select Property Manager (for inhouse)
                    ->waitFor('#work_order_property_manager_id:not([disabled])', 5)
                    ->select('#work_order_property_manager_id', $this->propertyManager->id)
                    
                    // Select Priority
                    ->select('#work_order_priority_id', $this->priority->id)
                    
                    // Select Unit
                    ->select('#work_order_unit_id', $this->blockUnit->id)
                    ->pause(1000) // Wait for issues to load
                    
                    // Select Issue (should be populated after unit selection)
                    ->waitFor('#block_issue_id_select:not([disabled])', 5)
                    ->select('#block_issue_id_select', $this->blockIssue->id)
                    
                    // Select Status
                    ->select('#work_order_status', '1') // Pending
                    
                    // Add optional comment
                    ->type('#work_order_comment', 'Test inhouse work order comment')
                    
                    // Submit the form
                    ->press('Create Work Order')
                    ->pause(2000) // Wait for AJAX submission and table refresh
                    
                    // Verify success - work order should appear in the table
                    ->assertSee('Work Orders')
                    ->assertSee($this->blockIssue->ref_no ?? 'Test Issue')
                    ->assertSee('Normal') // Priority label
                    ->assertSee('Pending'); // Status
        });
    }

    public function test_work_order_creation_requires_all_mandatory_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->assertSee('Edit Block')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(500)
                    ->assertSee('Work Orders')
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500)
                    ->assertSee('Create Work Order')
                    
                    // Try to submit without filling required fields
                    ->press('Create Work Order')
                    ->pause(500)
                    
                    // Should show validation errors (form validation)
                    // The form should not submit and modal should remain open
                    ->assertPresent('#workOrderModal')
                    ->assertSee('Create Work Order');
        });
    }

    public function test_work_order_modal_resets_after_creation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->assertSee('Edit Block')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(500)
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500)
                    
                    // Fill and submit form
                    ->select('#work_order_contract_company_id', $this->contractCompany->id)
                    ->select('#work_order_priority_id', $this->priority->id)
                    ->select('#work_order_unit_id', $this->blockUnit->id)
                    ->pause(1000)
                    ->waitFor('#block_issue_id_select:not([disabled])', 5)
                    ->select('#block_issue_id_select', $this->blockIssue->id)
                    ->select('#work_order_status', '1')
                    ->press('Create Work Order')
                    ->pause(2000)
                    
                    // Modal should be closed after successful creation
                    ->assertMissing('#workOrderModal.show')
                    
                    // Open modal again - should be reset
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500)
                    ->assertValue('#work_order_type', 'outsource')
                    ->assertValue('#work_order_unit_id', '')
                    ->assertValue('#work_order_priority_id', '');
        });
    }

    public function test_issue_dropdown_loads_after_unit_selection()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->assertSee('Edit Block')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(500)
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500)
                    
                    // Initially, issue dropdown should be disabled
                    ->assertAttribute('#block_issue_id_select', 'disabled', 'true')
                    ->assertSee('Select a unit first to see issues')
                    
                    // Select unit
                    ->select('#work_order_unit_id', $this->blockUnit->id)
                    ->pause(1000)
                    
                    // Issue dropdown should now be enabled and populated
                    ->waitFor('#block_issue_id_select:not([disabled])', 5)
                    ->assertNotAttribute('#block_issue_id_select', 'disabled', 'true')
                    ->assertSee($this->blockIssue->ref_no ?? 'Test Issue');
        });
    }

    public function test_work_order_type_toggle_shows_correct_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/' . $this->block->id . '/edit')
                    ->assertSee('Edit Block')
                    
                    // Click on Work Orders tab
                    ->click('#work-orders-tab')
                    ->pause(500)
                    
                    // Click Create Work Order button
                    ->click('button[data-bs-target="#workOrderModal"]')
                    ->pause(500)
                    
                    // Default should be "outsource" - Contract Company should be visible
                    ->assertVisible('#contractCompanyFieldContainer')
                    ->assertMissing('#propertyManagerFieldContainer:not([style*="display: none"])')
                    
                    // Change to "inhouse"
                    ->select('#work_order_type', 'inhouse')
                    ->pause(500)
                    
                    // Property Manager should be visible, Contract Company should be hidden
                    ->waitFor('#propertyManagerFieldContainer:not([style*="display: none"])', 5)
                    ->assertVisible('#propertyManagerFieldContainer')
                    ->assertMissing('#contractCompanyFieldContainer:not([style*="display: none"])')
                    
                    // Change back to "outsource"
                    ->select('#work_order_type', 'outsource')
                    ->pause(500)
                    
                    // Contract Company should be visible again
                    ->waitFor('#contractCompanyFieldContainer:not([style*="display: none"])', 5)
                    ->assertVisible('#contractCompanyFieldContainer');
        });
    }
}

