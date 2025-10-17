<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\BlockBuilding;
use App\Models\BlockBuildingType;
use App\Models\BlockUnit;
use App\Models\BlockUnitType;
use App\Models\User;
use App\Models\UserType;
use App\Models\Country;
use App\Models\State;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockBuildingsAndUnitsTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $block;
    protected $blockType;
    protected $country;
    protected $state;
    protected $blockBuildingType;
    protected $unitType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user type and admin user
        $adminType = UserType::factory()->admin()->create();
        $this->admin = User::factory()->withUserType($adminType->id)->create();
        
        // Create supporting data
        $this->blockType = BlockType::factory()->create();
        $this->country = Country::factory()->create();
        $this->state = State::factory()->create(['country_id' => $this->country->id]);
        $this->blockBuildingType = BlockBuildingType::factory()->create();
        $this->unitType = BlockUnitType::factory()->create();
        
        // Create a block for testing
        $this->block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id,
            'country_id' => $this->country->id,
            'state_id' => $this->state->id,
        ]);
    }

    /** @test */
    public function admin_can_view_buildings_tab()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    ->click('@tab-building-core')
                    ->pause(500)
                    ->waitFor('@building-core-content')
                    ->assertVisible('@building-core-content')
                    ->assertSee('Building Information');
        });
    }

    /** @test */
    public function admin_can_add_building_from_edit_page()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->click('@tab-building-core')
                    ->pause(500)
                    ->assertSee('Add Building')
                    ->click('button[data-bs-target="#buildingModal"]')
                    ->whenAvailable('#buildingModal', function ($modal) {
                        $modal->assertSee('Add Building')
                              ->select('building_type_id', $this->blockBuildingType->id)
                              ->type('name', 'Test Building A')
                              ->type('floor_no', '5')
                              ->type('roof_type', 'Flat Roof')
                              ->type('no_lift', '2')
                              ->press('Save Building')
                              ->pause(1000);
                    })
                    ->pause(2000)
                    ->assertSee('Test Building A');
            
            // Verify building was created in database
            $this->assertDatabaseHas('block_buildings', [
                'block_id' => $this->block->id,
                'name' => 'Test Building A',
                'floor_no' => 5,
                'no_lift' => 2,
            ]);
        });
    }

    /** @test */
    public function admin_can_view_units_tab()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    ->click('@tab-units')
                    ->pause(500)
                    ->waitFor('@units-content')
                    ->assertVisible('@units-content')
                    ->assertSee('Units');
        });
    }

    /** @test */
    public function admin_can_add_unit_from_edit_page()
    {
        // First create a building
        $building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'building_type_id' => $this->blockBuildingType->id,
        ]);

        $this->browse(function (Browser $browser) use ($building) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->click('@tab-units')
                    ->pause(500)
                    ->assertSee('Add Unit')
                    ->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) use ($building) {
                        $modal->assertSee('Add Unit')
                              ->select('block_building_id', $building->id)
                              ->select('block_unit_type_id', $this->unitType->id)
                              ->type('unit_code', '101')
                              ->type('unit_name', 'Unit 101')
                              ->press('Save Unit')
                              ->pause(1000);
                    })
                    ->pause(2000)
                    ->assertSee('101');
            
            // Verify unit was created in database
            $this->assertDatabaseHas('block_units', [
                'block_id' => $this->block->id,
                'block_building_id' => $building->id,
                'unit_code' => '101',
            ]);
        });
    }

    /** @test */
    public function buildings_and_units_display_correctly()
    {
        // Create test data
        $building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'building_type_id' => $this->blockBuildingType->id,
            'name' => 'Building Alpha',
            'floor_no' => 10,
            'no_lift' => 3,
        ]);

        $unit = BlockUnit::factory()->create([
            'block_id' => $this->block->id,
            'block_building_id' => $building->id,
            'block_unit_type_id' => $this->unitType->id,
            'unit_code' => '205',
        ]);

        $this->browse(function (Browser $browser) use ($building, $unit) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    
                    // Check Buildings tab loads
                    ->click('@tab-building-core')
                    ->pause(1000)
                    ->waitFor('@building-core-content')
                    ->assertVisible('@building-core-content')
                    ->assertSee('Building Information')
                    
                    // Check Units tab loads
                    ->click('@tab-units')
                    ->pause(1000)
                    ->waitFor('@units-content')
                    ->assertVisible('@units-content');
            
            // Verify data exists in database
            $this->assertDatabaseHas('block_buildings', [
                'id' => $building->id,
                'name' => 'Building Alpha',
            ]);
            
            $this->assertDatabaseHas('block_units', [
                'id' => $unit->id,
                'unit_code' => '205',
            ]);
        });
    }
}

