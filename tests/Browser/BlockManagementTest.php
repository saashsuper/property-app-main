<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\User;
use App\Models\UserType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $blockType;
    protected $country;
    protected $state;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user type and admin user
        $adminType = UserType::factory()->admin()->create();
        $this->admin = User::factory()->withUserType($adminType->id)->create();
        $this->blockType = BlockType::factory()->create();
        
        // Create country and state
        $this->country = Country::factory()->create();
        $this->state = State::factory()->create(['country_id' => $this->country->id]);
    }

    /** @test */
    public function admin_can_view_blocks_index_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->assertSee('Blocks')
                    ->assertSee('Add New Block')
                    ->assertPresent('@blocks-table');
        });
    }

    /** @test */
    public function admin_can_create_new_block()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->click('@add-block-button')
                    ->assertPathIs('/blocks/create')
                    ->type('name', 'Test Block via Browser')
                    ->type('management_company', 'Test Management Company')
                    ->select('block_type_id', $this->blockType->id)
                    ->type('block_address', '123 Test Street, Test City')
                    ->type('management_company_address', '456 Management Ave')
                    ->select('country_id', $this->country->id)
                    ->select('state_id', $this->state->id)
                    ->type('car_spaces', '50')
                    ->type('inspection_count', '12')
                    ->type('no_of_units', '100')
                    ->click('@submit-button')
                    ->waitForLocation('/blocks')
                    ->assertSee('Block created successfully')
                    ->assertSee('Test Block via Browser');
        });
    }

    /** @test */
    public function admin_can_view_block_details()
    {
        $block = Block::factory()->create([
            'name' => 'Test Block Details',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$block->id}")
                    ->assertSee($block->name)
                    ->assertSee($block->management_company)
                    ->assertSee($block->block_address)
                    ->assertPresent('@block-details-card')
                    ->assertPresent('@block-statistics-card');
        });
    }

    /** @test */
    public function admin_can_edit_block()
    {
        $block = Block::factory()->create([
            'name' => 'Original Block Name',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$block->id}/edit")
                    ->assertInputValue('name', $block->name)
                    ->clear('name')
                    ->type('name', 'Updated Block Name via Browser')
                    ->clear('car_spaces')
                    ->type('car_spaces', '75')
                    ->click('@update-button')
                    ->waitForLocation("/blocks")
                    ->pause(1000);
                    
            // Verify block was updated in database
            $block->refresh();
            $this->assertEquals('Updated Block Name via Browser', $block->name);
            $this->assertEquals(75, $block->car_spaces);
        });
    }

    /** @test */
    public function admin_can_delete_block()
    {
        $block = Block::factory()->create([
            'name' => 'Block to Delete',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->pause(2000) // Wait for DataTable to load
                    ->assertSee($block->name)
                    ->click("@delete-block-{$block->id}")
                    ->acceptDialog()
                    ->pause(3000) // Wait for deletion and page reload
                    ->assertPathIs('/blocks');
                    
            // Verify block was deleted from database
            $this->assertSoftDeleted('blocks', ['id' => $block->id]);
        });
    }

    /** @test */
    public function admin_can_search_blocks()
    {
        $block1 = Block::factory()->create([
            'name' => 'Searchable Block One',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $block2 = Block::factory()->create([
            'name' => 'Another Block Two',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) use ($block1, $block2) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->assertSee($block1->name)
                    ->assertSee($block2->name)
                    ->type('@search-input', 'Searchable')
                    ->keys('@search-input', '{enter}')
                    ->waitFor('@blocks-table')
                    ->assertSee($block1->name)
                    ->assertDontSee($block2->name);
        });
    }

    /** @test */
    public function admin_can_upload_images_to_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$block->id}")
                    ->assertPresent('@image-gallery')
                    ->click('@upload-images-button')
                    ->whenAvailable('#uploadImagesModal', function ($modal) {
                        $modal->assertSee('Upload Block Images')
                              ->assertPresent('@dropzone-area');
                    })
                    ->click('.btn-close')
                    ->waitUntilMissing('#uploadImagesModal');
        });
    }

    /** @test */
    public function admin_can_navigate_block_tabs()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$block->id}")
                    ->assertSee('Block Information')
                    ->assertPresent('@block-info-content')
                    
                    // Test Building Core tab
                    ->click('@tab-building-core')
                    ->pause(500)
                    ->waitFor('@building-core-content')
                    ->assertVisible('@building-core-content')
                    
                    // Test Contractors tab
                    ->click('@tab-contractors')
                    ->pause(500)
                    ->waitFor('@contractors-content')
                    ->assertVisible('@contractors-content')
                    
                    // Test Units tab
                    ->click('@tab-units')
                    ->pause(500)
                    ->waitFor('@units-content')
                    ->assertVisible('@units-content')
                    
                    // Test Site Visit tab
                    ->click('@tab-site-visit')
                    ->pause(500)
                    ->waitFor('@site-visit-content')
                    ->assertVisible('@site-visit-content')
                    
                    // Test Inspections tab
                    ->click('@tab-inspections')
                    ->pause(500)
                    ->waitFor('@inspections-content')
                    ->assertVisible('@inspections-content')
                    
                    // Test Issues tab
                    ->click('@tab-issues')
                    ->pause(500)
                    ->waitFor('@issues-content')
                    ->assertVisible('@issues-content')
                    
                    // Test Work Orders tab
                    ->click('@tab-work-orders')
                    ->pause(500)
                    ->waitFor('@work-orders-content')
                    ->assertVisible('@work-orders-content')
                    
                    // Return to Block Information tab
                    ->click('@tab-block-info')
                    ->pause(500)
                    ->waitFor('@block-info-content')
                    ->assertVisible('@block-info-content');
        });
    }

    /** @test */
    public function form_validation_works_for_block_creation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/create')
                    ->click('@submit-button')
                    ->waitForText('The name field is required')
                    ->assertSee('The management company field is required')
                    ->assertSee('The block type field is required')
                    ->assertSee('The block address field is required')
                    ->assertSee('The country field is required')
                    ->assertSee('The state field is required')
                    ->assertSee('The car spaces field is required');
        });
    }

    /** @test */
    public function country_state_dropdown_dependency_works()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks/create')
                    ->select('country_id', '1') // Assuming country ID 1 exists
                    ->waitFor('select[name="state_id"] option:not([value=""])')
                    ->assertSelectHasOptions('state_id', ['1', '2']); // Assuming states exist
        });
    }

    /** @test */
    public function admin_can_filter_blocks_by_type()
    {
        $blockType1 = BlockType::factory()->create(['name' => 'Residential']);
        $blockType2 = BlockType::factory()->create(['name' => 'Commercial']);

        $block1 = Block::factory()->create([
            'name' => 'Residential Block',
            'block_type_id' => $blockType1->id,
            'created_by' => $this->admin->id
        ]);

        $block2 = Block::factory()->create([
            'name' => 'Commercial Block',
            'block_type_id' => $blockType2->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) use ($block1, $block2, $blockType1) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->assertSee($block1->name)
                    ->assertSee($block2->name)
                    ->select('@filter-block-type', $blockType1->id)
                    ->waitFor('@blocks-table')
                    ->assertSee($block1->name)
                    ->assertDontSee($block2->name);
        });
    }

    /** @test */
    public function admin_can_export_blocks_data()
    {
        Block::factory()->count(3)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->click('@export-button')
                    ->whenAvailable('.dropdown-menu', function ($menu) {
                        $menu->assertSee('Export to Excel')
                             ->assertSee('Export to PDF')
                             ->assertSee('Export to CSV');
                    });
        });
    }

    /** @test */
    public function responsive_design_works_on_mobile()
    {
        $this->browse(function (Browser $browser) {
            $browser->resize(375, 667) // iPhone 6/7/8 size
                    ->loginAs($this->admin)
                    ->visit('/blocks')
                    ->assertPresent('@mobile-menu-toggle')
                    ->assertPresent('@blocks-table')
                    ->click('@mobile-menu-toggle')
                    ->assertVisible('@mobile-navigation');
        });
    }

    /** @test */
    public function pagination_works_with_many_blocks()
    {
        Block::factory()->count(25)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/blocks')
                    ->assertPresent('@pagination')
                    ->assertSee('Next')
                    ->click('@next-page')
                    ->waitForLocation('/blocks?page=2')
                    ->assertSee('Previous');
        });
    }

    /** @test */
    public function block_statistics_display_correctly()
    {
        $block = Block::factory()->create([
            'car_spaces' => 50,
            'no_of_units' => 100,
            'inspection_count' => 12,
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $this->browse(function (Browser $browser) use ($block) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$block->id}")
                    ->within('@statistics-card', function ($card) {
                        $card->assertSee('50') // car spaces
                             ->assertSee('100') // units
                             ->assertSee('12'); // inspections
                    });
        });
    }
}
