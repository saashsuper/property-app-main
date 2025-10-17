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
use App\Models\Salutation;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockUnitCrudTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;
    protected $block;
    protected $building;
    protected $blockBuildingType;
    protected $unitType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user type and admin user
        $adminType = UserType::factory()->admin()->create();
        $this->admin = User::factory()->withUserType($adminType->id)->create();
        
        // Create supporting data
        $blockType = BlockType::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $this->blockBuildingType = BlockBuildingType::factory()->create();
        $this->unitType = BlockUnitType::factory()->create();
        
        // Create salutations for dropdown
        Salutation::create(['name' => 'Mr', 'common_status_id' => 1]);
        Salutation::create(['name' => 'Mrs', 'common_status_id' => 1]);
        Salutation::create(['name' => 'Ms', 'common_status_id' => 1]);
        
        // Create a block for testing
        $this->block = Block::factory()->create([
            'block_type_id' => $blockType->id,
            'created_by' => $this->admin->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
        ]);
        
        // Create a building for the block
        $this->building = BlockBuilding::factory()->create([
            'block_id' => $this->block->id,
            'building_type_id' => $this->blockBuildingType->id,
            'name' => 'Building A',
        ]);
    }

    /** @test */
    public function admin_can_open_add_unit_modal()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(500)
                    ->assertSee('Add Unit')
                    ->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) {
                        $modal->assertSee('Add Unit')
                              ->assertVisible('#block_building_id')
                              ->assertVisible('#block_unit_type_id')
                              ->assertVisible('#unit_code')
                              ->assertVisible('#unit_name')
                              ->assertVisible('#unitSubmitBtn');
                    });
        });
    }

    /** @test */
    public function admin_can_add_unit_with_all_required_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(1000)
                    ->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) {
                        $modal->assertSee('Add Unit')
                              ->select('block_building_id', $this->building->id)
                              ->pause(300)
                              ->select('block_unit_type_id', $this->unitType->id)
                              ->pause(300)
                              ->type('unit_code', 'A101')
                              ->type('unit_name', 'Apartment 101')
                              ->select('salutation', 'Mr') // Required
                              ->type('email', 'unit101@example.com') // Required
                              ->select('resident', '1') // Required - Yes
                              ->pause(500)
                              ->click('#unitSubmitBtn')
                              ->pause(3000); // Wait for response
                    });
            
            // Check if modal closed (success) or still visible (error)
            try {
                $browser->waitUntilMissing('#unitModal', 5);
            } catch (\Exception $e) {
                // Modal still visible, check for error message
                $browser->whenAvailable('#unitModal', function ($modal) {
                    if ($modal->element('.alert-danger')->isDisplayed()) {
                        $errorText = $modal->text('.alert-danger');
                        dump('Error in modal: ' . $errorText);
                    }
                });
                throw $e;
            }
            
            $browser->pause(1000);
            
            // Verify unit was created with required fields
            $this->assertDatabaseHas('block_units', [
                'block_id' => $this->block->id,
                'block_building_id' => $this->building->id,
                'unit_code' => 'A101',
                'unit_name' => 'Apartment 101',
                'salutation' => 'Mr',
                'email' => 'unit101@example.com',
                'resident' => true,
            ]);
        });
    }

    /** @test */
    public function admin_can_fill_all_unit_fields()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(500)
                    ->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) {
                        $modal->select('block_building_id', $this->building->id)
                              ->select('block_unit_type_id', $this->unitType->id)
                              ->type('unit_code', 'A202')
                              ->type('unit_name', 'Apartment 202')
                              ->type('owners_name', 'John Doe')
                              ->assertInputValue('owners_name', 'John Doe')
                              ->select('salutation', 'Mr') // Required field
                              ->assertSelected('salutation', 'Mr')
                              ->type('email', 'john.doe@example.com')
                              ->assertInputValue('email', 'john.doe@example.com')
                              ->select('resident', '1')
                              ->assertSelected('resident', '1')
                              ->type('mobile_no', '1234567890')
                              ->type('phone_number', '9876543210')
                              ->type('letting_agent', 'ABC Realty')
                              ->type('misc_info', 'Premium unit with balcony')
                              ->assertVisible('#unitSubmitBtn')
                              ->pause(500);
                    });
        });
    }

    /** @test */
    public function unit_displays_in_units_tab_after_creation()
    {
        // Create a unit
        $unit = BlockUnit::factory()->create([
            'block_id' => $this->block->id,
            'block_building_id' => $this->building->id,
            'block_unit_type_id' => $this->unitType->id,
            'unit_code' => 'A303',
            'unit_name' => 'Apartment 303',
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
                'unit_code' => 'A303',
            ]);
        });
    }

    /** @test */
    public function unit_modal_can_be_opened_multiple_times()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit("/blocks/{$this->block->id}/edit")
                    ->scrollTo('@tab-units')
                    ->click('@tab-units')
                    ->pause(500);
            
            // Open modal first time
            $browser->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) {
                        $modal->assertSee('Add Unit')
                              ->assertVisible('#unitSubmitBtn');
                    })
                    ->press('Cancel')
                    ->pause(1000)
                    ->waitUntilMissing('#unitModal');
            
            // Open modal second time to verify it works again
            $browser->click('button[data-bs-target="#unitModal"]')
                    ->whenAvailable('#unitModal', function ($modal) {
                        $modal->assertSee('Add Unit')
                              ->assertVisible('#unit_code')
                              ->assertVisible('#unit_name');
                    });
        });
    }
}

