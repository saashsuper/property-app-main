<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\Block;
use App\Models\BlockInspection;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockInspectionsTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_block_inspections_list()
    {
        // Create test data
        $block = Block::factory()->create(['name' => 'Test Block']);
        $inspection1 = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010001',
            'notes' => 'Test inspection 1'
        ]);
        $inspection2 = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010002',
            'notes' => 'Test inspection 2'
        ]);

        $this->browse(function (Browser $browser) use ($inspection1, $inspection2) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Block Inspections List')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    ->assertSee('Test Block')
                    ->assertSee('Test inspection 1')
                    ->assertSee('Test inspection 2');
        });
    }

    public function test_admin_can_create_new_inspection()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $user1 = User::factory()->create(['name' => 'Inspector 1']);
        $user2 = User::factory()->create(['name' => 'Inspector 2']);
        $leadInspector = User::factory()->create(['name' => 'Lead Inspector']);

        $this->browse(function (Browser $browser) use ($block, $user1, $user2, $leadInspector) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Block Inspections List')
                    
                    // Click Schedule Inspection button
                    ->clickLink('Schedule Inspection')
                    ->assertPathIs('/block-inspections/create')
                    ->assertSee('Schedule Block Inspection')
                    
                    // Fill in inspection details
                    ->select('block_id', $block->id)
                    ->type('scheduled_date_time', now()->addDay()->format('Y-m-d H:i'))
                    ->type('notes', 'Test inspection notes')
                    
                    // Select team members
                    ->check('input[name="team_members[]"][value="' . $user1->id . '"]')
                    ->check('input[name="team_members[]"][value="' . $user2->id . '"]')
                    
                    // Select lead inspector
                    ->select('lead_inspector', $leadInspector->id)
                    
                    // Submit the form
                    ->press('Schedule Inspection')
                    ->assertPathIs('/block-inspections')
                    ->assertSee('Inspection scheduled successfully')
                    ->assertSee('Test Block');
        });
    }

    public function test_admin_can_edit_existing_inspection()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $inspection = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'notes' => 'Original notes'
        ]);
        $newUser = User::factory()->create(['name' => 'New Inspector']);

        $this->browse(function (Browser $browser) use ($inspection, $newUser) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Original notes')
                    
                    // Click Edit button
                    ->click('@edit-inspection-' . $inspection->id)
                    ->assertPathIs('/block-inspections/' . $inspection->id . '/edit')
                    ->assertSee('Edit Block Inspection')
                    
                    // Update inspection details
                    ->clear('notes')
                    ->type('notes', 'Updated inspection notes')
                    ->select('user_id', $newUser->id)
                    ->type('scheduled_date_time', now()->addDays(2)->format('Y-m-d H:i'))
                    
                    // Submit the form
                    ->press('Update Inspection')
                    ->assertPathIs('/block-inspections')
                    ->assertSee('Inspection updated successfully')
                    ->assertSee('Updated inspection notes');
        });
    }

    public function test_admin_can_delete_inspection()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $inspection = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010001'
        ]);

        $this->browse(function (Browser $browser) use ($inspection) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('INSP2024010001')
                    
                    // Click Delete button
                    ->click('@delete-inspection-' . $inspection->id)
                    ->whenAvailable('.modal', function ($modal) {
                        $modal->assertSee('Are you sure you want to delete this inspection?')
                              ->press('Delete');
                    })
                    ->assertSee('Inspection deleted successfully')
                    ->assertDontSee('INSP2024010001');
        });
    }

    public function test_inspection_status_filter_works()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $scheduledInspection = BlockInspection::factory()->scheduled()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010001'
        ]);
        $completedInspection = BlockInspection::factory()->completed()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010002'
        ]);

        $this->browse(function (Browser $browser) use ($scheduledInspection, $completedInspection) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    
                    // Filter by Scheduled status
                    ->select('status', '1')
                    ->press('Filter')
                    ->assertSee('INSP2024010001')
                    ->assertDontSee('INSP2024010002')
                    
                    // Filter by Completed status
                    ->select('status', '3')
                    ->press('Filter')
                    ->assertDontSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    
                    // Clear filter
                    ->select('status', '')
                    ->press('Filter')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002');
        });
    }

    public function test_inspection_date_range_filter_works()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $todayInspection = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'scheduled_date_time' => now()->addDay(),
            'ref_no' => 'INSP2024010001'
        ]);
        $futureInspection = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'scheduled_date_time' => now()->addDays(5),
            'ref_no' => 'INSP2024010002'
        ]);

        $this->browse(function (Browser $browser) use ($todayInspection, $futureInspection) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    
                    // Filter by date range
                    ->type('date_from', now()->format('Y-m-d'))
                    ->type('date_to', now()->addDays(2)->format('Y-m-d'))
                    ->press('Filter')
                    ->assertSee('INSP2024010001')
                    ->assertDontSee('INSP2024010002')
                    
                    // Clear filters
                    ->clear('date_from')
                    ->clear('date_to')
                    ->press('Filter')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002');
        });
    }

    public function test_inspection_search_functionality()
    {
        $block = Block::factory()->create(['name' => 'Test Block']);
        $inspection1 = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010001',
            'notes' => 'First inspection'
        ]);
        $inspection2 = BlockInspection::factory()->create([
            'block_id' => $block->id,
            'ref_no' => 'INSP2024010002',
            'notes' => 'Second inspection'
        ]);

        $this->browse(function (Browser $browser) use ($inspection1, $inspection2) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    
                    // Search by reference number
                    ->type('input[name="search"]', 'INSP2024010001')
                    ->press('Search')
                    ->assertSee('INSP2024010001')
                    ->assertDontSee('INSP2024010002')
                    
                    // Search by notes
                    ->clear('input[name="search"]')
                    ->type('input[name="search"]', 'Second')
                    ->press('Search')
                    ->assertDontSee('INSP2024010001')
                    ->assertSee('INSP2024010002')
                    
                    // Clear search
                    ->clear('input[name="search"]')
                    ->press('Search')
                    ->assertSee('INSP2024010001')
                    ->assertSee('INSP2024010002');
        });
    }

    public function test_inspection_creation_form_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections/create')
                    ->assertSee('Schedule Block Inspection')
                    
                    // Try to submit empty form
                    ->press('Schedule Inspection')
                    ->assertSee('The block id field is required')
                    ->assertSee('The scheduled date time field is required')
                    ->assertSee('The team members field is required')
                    ->assertSee('The lead inspector field is required');
        });
    }

    public function test_inspection_creation_with_past_date()
    {
        $block = Block::factory()->create();
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($block, $user) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections/create')
                    ->assertSee('Schedule Block Inspection')
                    
                    // Fill in form with past date
                    ->select('block_id', $block->id)
                    ->type('scheduled_date_time', now()->subDay()->format('Y-m-d H:i'))
                    ->type('notes', 'Test inspection')
                    ->check('input[name="team_members[]"][value="' . $user->id . '"]')
                    ->select('lead_inspector', $user->id)
                    
                    // Submit the form
                    ->press('Schedule Inspection')
                    ->assertSee('The scheduled date time must be a date after now');
        });
    }

    public function test_inspection_pagination_works()
    {
        // Create more inspections than the pagination limit
        $block = Block::factory()->create();
        BlockInspection::factory()->count(25)->create(['block_id' => $block->id]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Block Inspections List')
                    
                    // Check if pagination is present
                    ->assertPresent('.pagination')
                    
                    // Navigate to next page
                    ->clickLink('Next')
                    ->assertSee('Block Inspections List')
                    
                    // Navigate back to first page
                    ->clickLink('Previous')
                    ->assertSee('Block Inspections List');
        });
    }

    public function test_inspection_export_buttons_work()
    {
        $block = Block::factory()->create();
        BlockInspection::factory()->count(5)->create(['block_id' => $block->id]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Block Inspections List')
                    
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

    public function test_inspection_status_badges_display_correctly()
    {
        $block = Block::factory()->create();
        $scheduledInspection = BlockInspection::factory()->scheduled()->create(['block_id' => $block->id]);
        $inProgressInspection = BlockInspection::factory()->inProgress()->create(['block_id' => $block->id]);
        $completedInspection = BlockInspection::factory()->completed()->create(['block_id' => $block->id]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/block-inspections')
                    ->assertSee('Block Inspections List')
                    
                    // Check status badges are present
                    ->assertSee('Scheduled')
                    ->assertSee('In Progress')
                    ->assertSee('Completed');
        });
    }
}
