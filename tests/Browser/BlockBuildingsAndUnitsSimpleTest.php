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

class BlockBuildingsAndUnitsSimpleTest extends DuskTestCase
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
    public function admin_can_navigate_to_buildings_and_units_tabs()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    
                    // Test Buildings Tab
                    ->scrollTo('@tab-building-core')
                    ->click('@tab-building-core')
                    ->pause(500)
                    ->waitFor('@building-core-content')
                    ->assertVisible('@building-core-content')
                    ->assertSee('Building Information')
                    
                    // Test Units Tab
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(500)
                    ->waitFor('@units-content')
                    ->assertVisible('@units-content');
        });
    }

    /** @test */
    public function buildings_display_when_they_exist()
    {
        // Create a building
        $building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'building_type_id' => $this->blockBuildingType->id,
            'name' => 'Test Building',
            'floor_no' => 5,
            'no_lift' => 2,
        ]);

        $this->browse(function (Browser $browser) use ($building) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    ->scrollTo('@tab-building-core')
                    ->click('@tab-building-core')
                    ->pause(1000)
                    ->waitFor('@building-core-content')
                    ->assertVisible('@building-core-content');
            
            // Verify building exists in database
            $this->assertDatabaseHas('block_buildings', [
                'id' => $building->id,
                'name' => 'Test Building',
                'floor_no' => 5,
                'no_lift' => 2,
            ]);
        });
    }

    /** @test */
    public function units_display_when_they_exist()
    {
        // Create building and unit
        $building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'building_type_id' => $this->blockBuildingType->id,
        ]);

        $unit = BlockUnit::factory()->create([
            'block_id' => $this->block->id,
            'block_building_id' => $building->id,
            'block_unit_type_id' => $this->unitType->id,
            'unit_code' => '101',
            'unit_name' => 'Unit 101',
        ]);

        $this->browse(function (Browser $browser) use ($unit) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(1000)
                    ->waitFor('@units-content')
                    ->assertVisible('@units-content');
            
            // Verify unit exists in database
            $this->assertDatabaseHas('block_units', [
                'id' => $unit->id,
                'unit_code' => '101',
                'unit_name' => 'Unit 101',
            ]);
        });
    }

    /** @test */
    public function admin_can_access_buildings_edit_tab()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-building-core')
                    ->click('@tab-building-core')
                    ->pause(500)
                    ->assertSee('Building/Core')
                    ->assertSee('Add Building');
        });
    }

    /** @test */
    public function admin_can_access_units_edit_tab()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(500)
                    ->assertSee('Units')
                    ->assertSee('Add Unit');
        });
    }
}

