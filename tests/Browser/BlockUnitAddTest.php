<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\BlockBuilding;
use App\Models\BlockBuildingType;
use App\Models\BlockUnitType;
use App\Models\User;
use App\Models\UserType;
use App\Models\Country;
use App\Models\State;
use App\Models\Salutation;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BlockUnitAddTest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function admin_can_add_unit_successfully()
    {
        // Setup
        $adminType = UserType::factory()->admin()->create();
        $admin = User::factory()->withUserType($adminType->id)->create();
        
        $blockType = BlockType::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $blockBuildingType = BlockBuildingType::factory()->create();
        $unitType = BlockUnitType::factory()->create();
        
        // Create salutations
        Salutation::create(['name' => 'Mr', 'common_status_id' => 1]);
        Salutation::create(['name' => 'Mrs', 'common_status_id' => 1]);
        
        $block = Block::factory()->create([
            'block_type_id' => $blockType->id,
            'created_by' => $admin->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
        ]);
        
        $building = BlockBuilding::factory()->create([
            'block_id' => $block->id,
            'building_type_id' => $blockBuildingType->id,
            'name' => 'Test Building',
        ]);

        $this->browse(function (Browser $browser) use ($admin, $block, $building, $unitType) {
            $browser->loginAs($admin)
                    ->visit("/blocks/{$block->id}/edit")
                    ->pause(2000)
                    ->waitFor('#units-tab', 10)
                    ->click('#units-tab')
                    ->pause(1500)
                    ->assertSee('Add Unit')
                    ->click('button[data-bs-target="#unitModal"]')
                    ->pause(1000)
                    ->whenAvailable('#unitModal', function ($modal) use ($building, $unitType) {
                        $modal->assertSee('Add Unit')
                              // Fill required fields
                              ->select('block_building_id', $building->id)
                              ->pause(500)
                              ->select('block_unit_type_id', $unitType->id)
                              ->pause(500)
                              ->type('unit_code', 'UNIT101')
                              ->pause(200)
                              ->type('unit_name', 'Apartment 101')
                              ->pause(200)
                              ->select('salutation', 'Mr')
                              ->pause(200)
                              ->type('email', 'test@unit101.com')
                              ->pause(200)
                              ->select('resident', '1')
                              ->pause(500)
                              // Click submit
                              ->press('Save')
                              ->pause(5000); // Wait for AJAX response
                    })
                    ->pause(3000);
                    
            // Verify modal closed (indicating success)
            $browser->assertMissing('#unitModal')
                    // Verify success message appears
                    ->pause(1000)
                    // Wait for DataTable to reload and show the new unit
                    ->pause(2000)
                    // Verify the unit appears in the table
                    ->assertSeeIn('.tab-pane.active', 'UNIT101');
        });
    }
}


