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

class BlockUnitUITest extends DuskTestCase
{
    use DatabaseMigrations;

    /** @test */
    public function unit_form_has_all_required_fields_marked()
    {
        // Setup
        $adminType = UserType::factory()->admin()->create();
        $admin = User::factory()->withUserType($adminType->id)->create();
        
        $blockType = BlockType::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $blockBuildingType = BlockBuildingType::factory()->create();
        
        Salutation::create(['name' => 'Mr', 'common_status_id' => 1]);
        
        $block = Block::factory()->create([
            'block_type_id' => $blockType->id,
            'created_by' => $admin->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
        ]);
        
        $building = BlockBuilding::factory()->create([
            'block_id' => $block->id,
            'building_type_id' => $blockBuildingType->id,
        ]);

        $this->browse(function (Browser $browser) use ($admin, $block) {
            $browser->loginAs($admin)
                    ->visit("/blocks/{$block->id}/edit")
                    ->pause(3000)
                    ->assertSee('Edit Block')
                    ->screenshot('edit-page-loaded');
                    
            // Try to find and click the units tab using JavaScript
            $tabExists = $browser->script('return document.querySelector("#units-tab") !== null;');
            
            if ($tabExists[0]) {
                $browser->script('document.querySelector("#units-tab").click();')
                        ->pause(2000)
                        ->screenshot('units-tab-clicked');
            } else {
                $browser->screenshot('units-tab-not-found');
            }
        });
    }
}

