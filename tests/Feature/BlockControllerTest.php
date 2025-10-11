<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\BlockImage;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BlockControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $contractorAdmin;
    protected $adminType;
    protected $contractorAdminType;
    protected $blockType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types
        $this->adminType = UserType::factory()->admin()->create();
        $this->contractorAdminType = UserType::factory()->contractorAdmin()->create();
        
        // Create users
        $this->admin = User::factory()->withUserType($this->adminType->id)->create();
        $this->contractorAdmin = User::factory()->withUserType($this->contractorAdminType->id)->create();
        
        // Create block type
        $this->blockType = BlockType::factory()->create();
        
        // Fake storage for testing
        Storage::fake('public');
    }

    /** @test */
    public function admin_can_view_blocks_index()
    {
        $blocks = Block::factory()->count(3)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('blocks.index'));

        $response->assertStatus(200)
                ->assertViewIs('blocks.index')
                ->assertViewHas('blocks');
    }

    /** @test */
    public function admin_can_create_block()
    {
        $blockData = [
            'name' => 'Test Block',
            'management_company' => 'Test Management Co.',
            'block_type_id' => $this->blockType->id,
            'block_manager_id' => $this->admin->id,
            'block_address' => '123 Test Street, Test City',
            'management_company_address' => '456 Management Ave, Business City',
            'country_id' => 1,
            'state_id' => 1,
            'car_spaces' => 50,
            'inspection_count' => 12,
            'no_of_units' => 100,
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), $blockData);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('blocks', [
            'name' => 'Test Block',
            'management_company' => 'Test Management Co.',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);
    }

    /** @test */
    public function admin_can_view_single_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('blocks.show', $block));

        $response->assertStatus(200)
                ->assertViewIs('blocks.show')
                ->assertViewHas('block', $block);
    }

    /** @test */
    public function admin_can_edit_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('blocks.edit', $block));

        $response->assertStatus(200)
                ->assertViewIs('blocks.edit')
                ->assertViewHas('block', $block);
    }

    /** @test */
    public function admin_can_update_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $updateData = [
            'name' => 'Updated Block Name',
            'management_company' => 'Updated Management Co.',
            'block_type_id' => $this->blockType->id,
            'block_manager_id' => $this->admin->id,
            'block_address' => 'Updated Address',
            'management_company_address' => 'Updated Management Address',
            'country_id' => 1,
            'state_id' => 1,
            'car_spaces' => 75,
            'inspection_count' => 24,
            'no_of_units' => 150,
        ];

        $response = $this->actingAs($this->admin)
                         ->put(route('blocks.update', $block), $updateData);

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'name' => 'Updated Block Name',
            'management_company' => 'Updated Management Co.',
            'car_spaces' => 75,
            'updated_by' => $this->admin->id
        ]);
    }

    /** @test */
    public function admin_can_delete_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->delete(route('blocks.destroy', $block));

        $response->assertRedirect()
                ->assertSessionHas('success');

        $this->assertSoftDeleted('blocks', [
            'id' => $block->id,
            'deleted_by' => $this->admin->id
        ]);
    }

    /** @test */
    public function block_creation_requires_validation()
    {
        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), []);

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
    public function block_creation_validates_data_types()
    {
        $invalidData = [
            'name' => str_repeat('a', 101), // Too long
            'management_company' => 'Test Management Co.',
            'block_type_id' => 'invalid',
            'block_address' => str_repeat('a', 501), // Too long
            'country_id' => 'invalid',
            'state_id' => 'invalid',
            'car_spaces' => 'invalid',
            'inspection_count' => 'invalid',
            'no_of_units' => 'invalid',
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.store'), $invalidData);

        $response->assertSessionHasErrors([
            'name',
            'block_type_id',
            'block_address',
            'country_id',
            'state_id',
            'car_spaces',
            'inspection_count',
            'no_of_units'
        ]);
    }

    /** @test */
    public function contractor_admin_cannot_create_blocks()
    {
        $blockData = [
            'name' => 'Test Block',
            'management_company' => 'Test Management Co.',
            'block_type_id' => $this->blockType->id,
            'block_address' => '123 Test Street',
            'country_id' => 1,
            'state_id' => 1,
            'car_spaces' => 50,
        ];

        $response = $this->actingAs($this->contractorAdmin)
                         ->post(route('blocks.store'), $blockData);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function guest_cannot_access_blocks()
    {
        $response = $this->get(route('blocks.index'));
        $response->assertRedirect(route('login'));
    }

    /** @test */
    public function admin_can_upload_multiple_images_to_block()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $images = [
            UploadedFile::fake()->image('test1.jpg', 800, 600)->size(1024), // 1MB
            UploadedFile::fake()->image('test2.png', 1024, 768)->size(2048), // 2MB
            UploadedFile::fake()->image('test3.gif', 640, 480)->size(512), // 512KB
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.images.upload', $block), [
                             'images' => $images
                         ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertDatabaseCount('block_images', 3);
        $this->assertDatabaseHas('block_images', [
            'block_id' => $block->id,
            'original_name' => 'test1.jpg'
        ]);
    }

    /** @test */
    public function image_upload_validates_file_size()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $largeImage = UploadedFile::fake()->image('large.jpg', 2000, 2000)->size(6144); // 6MB

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.images.upload', $block), [
                             'images' => [$largeImage]
                         ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors('images.0');
    }

    /** @test */
    public function image_upload_validates_total_size()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // Create 4 images of 4MB each = 16MB total (exceeds 15MB limit)
        $images = [
            UploadedFile::fake()->image('test1.jpg', 1500, 1500)->size(4096),
            UploadedFile::fake()->image('test2.jpg', 1500, 1500)->size(4096),
            UploadedFile::fake()->image('test3.jpg', 1500, 1500)->size(4096),
            UploadedFile::fake()->image('test4.jpg', 1500, 1500)->size(4096),
        ];

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.images.upload', $block), [
                             'images' => $images
                         ]);

        $response->assertStatus(422)
                ->assertJson(['success' => false]);
    }

    /** @test */
    public function admin_can_delete_block_image()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $image = BlockImage::factory()->create([
            'block_id' => $block->id,
            'uploaded_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->delete(route('blocks.images.delete', $block), [
                             'image_id' => $image->id
                         ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('block_images', [
            'id' => $image->id
        ]);
    }

    /** @test */
    public function admin_can_set_primary_image()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $image1 = BlockImage::factory()->create([
            'block_id' => $block->id,
            'is_primary' => true,
            'uploaded_by' => $this->admin->id
        ]);

        $image2 = BlockImage::factory()->create([
            'block_id' => $block->id,
            'is_primary' => false,
            'uploaded_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->post(route('blocks.images.primary', $block), [
                             'image_id' => $image2->id
                         ]);

        $response->assertStatus(200)
                ->assertJson(['success' => true]);

        $this->assertDatabaseHas('block_images', [
            'id' => $image2->id,
            'is_primary' => true
        ]);

        $this->assertDatabaseHas('block_images', [
            'id' => $image1->id,
            'is_primary' => false
        ]);
    }

    /** @test */
    public function admin_can_get_block_api_data()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('api.blocks.show', $block));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'management_company',
                    'block_address',
                    'car_spaces',
                    'no_of_units',
                    'created_at',
                    'updated_at'
                ]);
    }

    /** @test */
    public function admin_can_get_blocks_list_api()
    {
        Block::factory()->count(5)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->get(route('api.blocks'));

        $response->assertStatus(200)
                ->assertJsonCount(5);
    }

    /** @test */
    public function block_soft_delete_can_be_restored()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // Soft delete the block
        $this->actingAs($this->admin)
             ->delete(route('blocks.destroy', $block));

        $this->assertSoftDeleted('blocks', ['id' => $block->id]);

        // Restore the block
        $block->restore();

        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'deleted_at' => null
        ]);
    }
}
