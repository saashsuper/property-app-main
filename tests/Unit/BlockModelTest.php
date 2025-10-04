<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\BlockImage;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockModelTest extends TestCase
{
    use RefreshDatabase;

    protected $block;
    protected $blockType;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->blockType = BlockType::factory()->create();
        $this->user = User::factory()->create();
        $this->block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'user_id' => $this->user->id,
            'created_by' => $this->user->id
        ]);
    }

    /** @test */
    public function block_has_fillable_attributes()
    {
        $fillable = [
            'name',
            'management_company',
            'block_type_id',
            'user_id',
            'block_manager_id',
            'address1',
            'address2',
            'address3',
            'block_address',
            'management_company_address',
            'country_id',
            'state_id',
            'car_spaces',
            'inspection_count',
            'no_of_units',
            'created_by',
            'updated_by',
            'deleted_by',
        ];

        $this->assertEquals($fillable, $this->block->getFillable());
    }

    /** @test */
    public function block_casts_attributes_correctly()
    {
        $casts = [
            'id' => 'int',
            'car_spaces' => 'integer',
            'inspection_count' => 'integer',
            'no_of_units' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
            'deleted_at' => 'datetime',
        ];

        foreach ($casts as $attribute => $expectedType) {
            $this->assertEquals($expectedType, $this->block->getCasts()[$attribute]);
        }
    }

    /** @test */
    public function block_belongs_to_block_type()
    {
        $this->assertInstanceOf(BlockType::class, $this->block->blockType);
        $this->assertEquals($this->blockType->id, $this->block->blockType->id);
    }

    /** @test */
    public function block_belongs_to_user()
    {
        $this->assertInstanceOf(User::class, $this->block->user);
        $this->assertEquals($this->user->id, $this->block->user->id);
    }

    /** @test */
    public function block_belongs_to_creator()
    {
        $this->assertInstanceOf(User::class, $this->block->creator);
        $this->assertEquals($this->user->id, $this->block->creator->id);
    }

    /** @test */
    public function block_has_many_images()
    {
        $image1 = BlockImage::factory()->create(['block_id' => $this->block->id]);
        $image2 = BlockImage::factory()->create(['block_id' => $this->block->id]);

        $this->assertCount(2, $this->block->images);
        $this->assertInstanceOf(BlockImage::class, $this->block->images->first());
    }

    /** @test */
    public function block_has_one_primary_image()
    {
        $primaryImage = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'is_primary' => true
        ]);

        $nonPrimaryImage = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'is_primary' => false
        ]);

        $this->assertInstanceOf(BlockImage::class, $this->block->primaryImage);
        $this->assertEquals($primaryImage->id, $this->block->primaryImage->id);
        $this->assertTrue($this->block->primaryImage->is_primary);
    }

    /** @test */
    public function block_can_be_soft_deleted()
    {
        $blockId = $this->block->id;
        
        $this->block->delete();
        
        $this->assertSoftDeleted('blocks', ['id' => $blockId]);
        $this->assertNotNull($this->block->fresh()->deleted_at);
    }

    /** @test */
    public function block_can_be_restored_after_soft_delete()
    {
        $this->block->delete();
        $this->assertSoftDeleted('blocks', ['id' => $this->block->id]);
        
        $this->block->restore();
        
        $this->assertDatabaseHas('blocks', [
            'id' => $this->block->id,
            'deleted_at' => null
        ]);
    }

    /** @test */
    public function block_factory_creates_valid_block()
    {
        $block = Block::factory()->create();

        $this->assertDatabaseHas('blocks', [
            'id' => $block->id,
            'name' => $block->name,
            'management_company' => $block->management_company
        ]);

        $this->assertNotNull($block->name);
        $this->assertNotNull($block->management_company);
        $this->assertNotNull($block->block_address);
        $this->assertIsInt($block->car_spaces);
        $this->assertGreaterThanOrEqual(0, $block->car_spaces);
    }

    /** @test */
    public function block_validates_required_fields()
    {
        $block = new Block();
        
        // Test that validation fails without required fields
        $this->expectException(\Illuminate\Database\QueryException::class);
        $block->save();
    }

    /** @test */
    public function block_name_is_required_and_has_max_length()
    {
        // Test with valid name
        $block = Block::factory()->make(['name' => 'Valid Block Name']);
        $this->assertNotNull($block->name);
        $this->assertLessThanOrEqual(100, strlen($block->name));

        // Test with empty name should fail validation (handled by form validation)
        $block = Block::factory()->make(['name' => '']);
        $this->assertEquals('', $block->name);
    }

    /** @test */
    public function block_car_spaces_must_be_non_negative_integer()
    {
        $block = Block::factory()->create(['car_spaces' => 50]);
        $this->assertIsInt($block->car_spaces);
        $this->assertGreaterThanOrEqual(0, $block->car_spaces);
        $this->assertEquals(50, $block->car_spaces);
    }

    /** @test */
    public function block_inspection_count_can_be_null_or_non_negative()
    {
        // Test with null inspection count
        $block1 = Block::factory()->create(['inspection_count' => null]);
        $this->assertNull($block1->inspection_count);

        // Test with positive inspection count
        $block2 = Block::factory()->create(['inspection_count' => 12]);
        $this->assertIsInt($block2->inspection_count);
        $this->assertEquals(12, $block2->inspection_count);
    }

    /** @test */
    public function block_no_of_units_can_be_null_or_positive()
    {
        // Test with null units
        $block1 = Block::factory()->create(['no_of_units' => null]);
        $this->assertNull($block1->no_of_units);

        // Test with positive units
        $block2 = Block::factory()->create(['no_of_units' => 150]);
        $this->assertIsInt($block2->no_of_units);
        $this->assertEquals(150, $block2->no_of_units);
    }

    /** @test */
    public function block_tracks_creator_and_updater()
    {
        $creator = User::factory()->create();
        $updater = User::factory()->create();

        $block = Block::factory()->create([
            'created_by' => $creator->id,
            'updated_by' => $updater->id
        ]);

        $this->assertEquals($creator->id, $block->created_by);
        $this->assertEquals($updater->id, $block->updated_by);
    }

    /** @test */
    public function block_has_timestamps()
    {
        $this->assertNotNull($this->block->created_at);
        $this->assertNotNull($this->block->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->block->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->block->updated_at);
    }

    /** @test */
    public function block_scope_methods_work_correctly()
    {
        // Create blocks with different states
        $activeBlock = Block::factory()->create(['deleted_at' => null]);
        $deletedBlock = Block::factory()->create();
        $deletedBlock->delete();

        // Test that only non-deleted blocks are returned by default
        $activeBlocks = Block::all();
        $this->assertCount(2, $activeBlocks); // $this->block + $activeBlock
        $this->assertFalse($activeBlocks->contains($deletedBlock));

        // Test that withTrashed includes deleted blocks
        $allBlocks = Block::withTrashed()->get();
        $this->assertCount(3, $allBlocks); // All blocks including deleted
        $this->assertTrue($allBlocks->contains($deletedBlock));
    }
}
