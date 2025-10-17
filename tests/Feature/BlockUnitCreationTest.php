<?php

namespace Tests\Feature;

use Tests\TestCase;
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
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockUnitCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create salutations
        Salutation::create(['name' => 'Mr', 'common_status_id' => 1]);
        Salutation::create(['name' => 'Mrs', 'common_status_id' => 1]);
    }

    /** @test */
    public function unit_can_be_created_with_all_required_fields()
    {
        // Create admin user
        $adminType = UserType::factory()->admin()->create();
        $admin = User::factory()->withUserType($adminType->id)->create();
        
        // Create dependencies
        $blockType = BlockType::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        $blockBuildingType = BlockBuildingType::factory()->create();
        $unitType = BlockUnitType::factory()->create();
        
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

        // Act as admin and submit unit creation
        $response = $this->actingAs($admin)
            ->postJson(route('block-units.store'), [
                'block_id' => $block->id,
                'block_building_id' => $building->id,
                'block_unit_type_id' => $unitType->id,
                'unit_code' => 'UNIT101',
                'unit_name' => 'Apartment 101',
                'salutation' => 'Mr',
                'email' => 'test@unit101.com',
                'resident' => 1,
            ]);

        // Assert
        $response->assertStatus(200)
                 ->assertJson(['success' => true]);
        
        $this->assertDatabaseHas('block_units', [
            'block_id' => $block->id,
            'unit_code' => 'UNIT101',
            'unit_name' => 'Apartment 101',
            'salutation' => 'Mr',
            'email' => 'test@unit101.com',
            'resident' => true,
        ]);
    }

    /** @test */
    public function unit_creation_fails_without_required_fields()
    {
        // Create admin user
        $adminType = UserType::factory()->admin()->create();
        $admin = User::factory()->withUserType($adminType->id)->create();
        
        $blockType = BlockType::factory()->create();
        $country = Country::factory()->create();
        $state = State::factory()->create(['country_id' => $country->id]);
        
        $block = Block::factory()->create([
            'block_type_id' => $blockType->id,
            'created_by' => $admin->id,
            'country_id' => $country->id,
            'state_id' => $state->id,
        ]);

        // Try to create unit without required email field
        $response = $this->actingAs($admin)
            ->postJson(route('block-units.store'), [
                'block_id' => $block->id,
                'unit_code' => 'UNIT102',
                'unit_name' => 'Apartment 102',
                // Missing: email, salutation, resident, building
            ]);

        // Assert validation fails
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['block_building_id', 'email', 'salutation', 'resident']);
    }
}

