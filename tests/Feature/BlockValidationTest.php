<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockValidationTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $blockType;

    protected function setUp(): void
    {
        parent::setUp();
        
        $adminType = UserType::factory()->admin()->create();
        $this->admin = User::factory()->withUserType($adminType->id)->create();
        $this->blockType = BlockType::factory()->create();
    }

    /** @test */
    public function block_name_is_required()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function block_name_cannot_exceed_maximum_length()
    {
        $longName = str_repeat('a', 101); // 101 characters

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => $longName,
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function management_company_is_required()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('management_company');
    }

    /** @test */
    public function management_company_cannot_exceed_maximum_length()
    {
        $longCompanyName = str_repeat('a', 101); // 101 characters

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => $longCompanyName,
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('management_company');
    }

    /** @test */
    public function block_type_id_is_required()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('block_type_id');
    }

    /** @test */
    public function block_type_id_must_exist_in_block_types_table()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => 99999, // Non-existent ID
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('block_type_id');
    }

    /** @test */
    public function block_address_is_required()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('block_address');
    }

    /** @test */
    public function block_address_cannot_exceed_maximum_length()
    {
        $longAddress = str_repeat('a', 501); // 501 characters

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => $longAddress,
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('block_address');
    }

    /** @test */
    public function management_company_address_is_optional_but_has_max_length()
    {
        // Test with null management company address (should pass)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'management_company_address' => null,
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionDoesntHaveErrors('management_company_address');

        // Test with too long management company address (should fail)
        $longAddress = str_repeat('a', 501); // 501 characters
        
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 2',
                             'management_company' => 'Test Management 2',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address 2',
                             'management_company_address' => $longAddress,
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('management_company_address');
    }

    /** @test */
    public function country_id_is_required_and_must_be_integer()
    {
        // Test missing country_id
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('country_id');

        // Test non-integer country_id
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 'invalid',
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('country_id');
    }

    /** @test */
    public function state_id_is_required_and_must_be_integer()
    {
        // Test missing state_id
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('state_id');

        // Test non-integer state_id
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 'invalid',
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('state_id');
    }

    /** @test */
    public function car_spaces_is_required_and_must_be_non_negative_integer()
    {
        // Test missing car_spaces
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                         ]);

        $response->assertSessionHasErrors('car_spaces');

        // Test negative car_spaces
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => -5,
                         ]);

        $response->assertSessionHasErrors('car_spaces');

        // Test non-integer car_spaces
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 'invalid',
                         ]);

        $response->assertSessionHasErrors('car_spaces');
    }

    /** @test */
    public function inspection_count_is_optional_but_must_be_non_negative_integer_when_provided()
    {
        // Test with null inspection_count (should pass)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'inspection_count' => null,
                         ]);

        $response->assertSessionDoesntHaveErrors('inspection_count');

        // Test with negative inspection_count (should fail)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 2',
                             'management_company' => 'Test Management 2',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address 2',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'inspection_count' => -1,
                         ]);

        $response->assertSessionHasErrors('inspection_count');

        // Test with non-integer inspection_count (should fail)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 3',
                             'management_company' => 'Test Management 3',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address 3',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'inspection_count' => 'invalid',
                         ]);

        $response->assertSessionHasErrors('inspection_count');
    }

    /** @test */
    public function no_of_units_is_optional_but_must_be_non_negative_integer_when_provided()
    {
        // Test with null no_of_units (should pass)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'no_of_units' => null,
                         ]);

        $response->assertSessionDoesntHaveErrors('no_of_units');

        // Test with negative no_of_units (should fail)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 2',
                             'management_company' => 'Test Management 2',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address 2',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'no_of_units' => -1,
                         ]);

        $response->assertSessionHasErrors('no_of_units');

        // Test with non-integer no_of_units (should fail)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 3',
                             'management_company' => 'Test Management 3',
                             'block_type_id' => $this->blockType->id,
                             'block_address' => 'Test Address 3',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                             'no_of_units' => 'invalid',
                         ]);

        $response->assertSessionHasErrors('no_of_units');
    }

    /** @test */
    public function block_manager_id_is_optional_but_must_exist_when_provided()
    {
        $validManager = User::factory()->create();
        
        // Test with valid block_manager_id (should pass)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block',
                             'management_company' => 'Test Management',
                             'block_type_id' => $this->blockType->id,
                             'block_manager_id' => $validManager->id,
                             'block_address' => 'Test Address',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionDoesntHaveErrors('block_manager_id');

        // Test with non-existent block_manager_id (should fail)
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), [
                             'name' => 'Test Block 2',
                             'management_company' => 'Test Management 2',
                             'block_type_id' => $this->blockType->id,
                             'block_manager_id' => 99999, // Non-existent ID
                             'block_address' => 'Test Address 2',
                             'country_id' => 1,
                             'state_id' => 1,
                             'car_spaces' => 50,
                         ]);

        $response->assertSessionHasErrors('block_manager_id');
    }

    /** @test */
    public function update_validation_works_correctly()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // Test updating with invalid data
        $response = $this->actingAs($this->admin)
                         ->put(route('blocks.update', $block), [
                             'name' => '', // Empty name should fail
                             'management_company' => str_repeat('a', 101), // Too long
                             'block_type_id' => 99999, // Non-existent
                             'block_address' => '',
                             'country_id' => 'invalid',
                             'state_id' => 'invalid',
                             'car_spaces' => -5,
                         ]);

        $response->assertSessionHasErrors([
            'name',
            'management_company',
            'block_type_id',
            'block_address',
            'country_id',
            'state_id',
            'car_spaces'
        ]);
    }

    /** @test */
    public function validation_messages_are_user_friendly()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), []);

        $response->assertSessionHasErrors();
        
        // Check that validation messages exist and are helpful
        $errors = session('errors');
        $this->assertNotNull($errors);
        $this->assertTrue($errors->has('name'));
        $this->assertTrue($errors->has('management_company'));
        $this->assertTrue($errors->has('block_type_id'));
        $this->assertTrue($errors->has('block_address'));
        $this->assertTrue($errors->has('country_id'));
        $this->assertTrue($errors->has('state_id'));
        $this->assertTrue($errors->has('car_spaces'));
    }
}
