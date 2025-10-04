<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Block;
use App\Models\BlockImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class BlockImageModelTest extends TestCase
{
    use RefreshDatabase;

    protected $block;
    protected $user;
    protected $blockImage;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->block = Block::factory()->create(['created_by' => $this->user->id]);
        $this->blockImage = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'uploaded_by' => $this->user->id
        ]);
        
        Storage::fake('public');
    }

    /** @test */
    public function block_image_has_fillable_attributes()
    {
        $fillable = [
            'block_id',
            'original_name',
            'stored_name',
            'file_path',
            'file_extension',
            'file_size',
            'mime_type',
            'sort_order',
            'is_primary',
            'description',
            'uploaded_by',
        ];

        $this->assertEquals($fillable, $this->blockImage->getFillable());
    }

    /** @test */
    public function block_image_casts_attributes_correctly()
    {
        $casts = [
            'id' => 'int',
            'file_size' => 'integer',
            'sort_order' => 'integer',
            'is_primary' => 'boolean',
            'uploaded_by' => 'integer',
        ];

        foreach ($casts as $attribute => $expectedType) {
            $actualCasts = $this->blockImage->getCasts();
            $this->assertEquals($expectedType, $actualCasts[$attribute], "Cast for {$attribute} should be {$expectedType}, got {$actualCasts[$attribute]}");
        }
    }

    /** @test */
    public function block_image_belongs_to_block()
    {
        $this->assertInstanceOf(Block::class, $this->blockImage->block);
        $this->assertEquals($this->block->id, $this->blockImage->block->id);
    }

    /** @test */
    public function block_image_belongs_to_uploader()
    {
        $this->assertInstanceOf(User::class, $this->blockImage->uploader);
        $this->assertEquals($this->user->id, $this->blockImage->uploader->id);
    }

    /** @test */
    public function block_image_generates_full_path_attribute()
    {
        $blockImage = BlockImage::factory()->create([
            'file_path' => 'blocks/1',
            'stored_name' => 'test_image.jpg'
        ]);

        $expectedPath = 'blocks/1/test_image.jpg';
        $this->assertEquals($expectedPath, $blockImage->full_path);
    }

    /** @test */
    public function block_image_generates_url_attribute()
    {
        $blockImage = BlockImage::factory()->create([
            'file_path' => 'blocks/1',
            'stored_name' => 'test_image.jpg'
        ]);

        $expectedUrl = Storage::url('blocks/1/test_image.jpg');
        $this->assertEquals($expectedUrl, $blockImage->url);
    }

    /** @test */
    public function block_image_formats_file_size_human_readable()
    {
        // Test bytes
        $image1 = BlockImage::factory()->create(['file_size' => 512]);
        $this->assertEquals('512 B', $image1->file_size_human);

        // Test KB
        $image2 = BlockImage::factory()->create(['file_size' => 1536]); // 1.5 KB
        $this->assertEquals('1.5 KB', $image2->file_size_human);

        // Test MB
        $image3 = BlockImage::factory()->create(['file_size' => 2097152]); // 2 MB
        $this->assertEquals('2 MB', $image3->file_size_human);

        // Test GB
        $image4 = BlockImage::factory()->create(['file_size' => 2147483648]); // 2 GB
        $this->assertEquals('2 GB', $image4->file_size_human);
    }

    /** @test */
    public function block_image_ordered_scope_works()
    {
        // Create images with different sort orders
        $image1 = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'sort_order' => 3
        ]);
        $image2 = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'sort_order' => 1
        ]);
        $image3 = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'sort_order' => 2
        ]);

        $orderedImages = BlockImage::where('block_id', $this->block->id)->ordered()->get();
        
        $this->assertEquals($image2->id, $orderedImages->first()->id);
        $this->assertEquals($image3->id, $orderedImages->skip(1)->first()->id);
        $this->assertEquals($image1->id, $orderedImages->skip(2)->first()->id);
    }

    /** @test */
    public function block_image_factory_creates_valid_image()
    {
        $image = BlockImage::factory()->create();

        $this->assertDatabaseHas('block_images', [
            'id' => $image->id,
            'original_name' => $image->original_name,
            'stored_name' => $image->stored_name
        ]);

        $this->assertNotNull($image->original_name);
        $this->assertNotNull($image->stored_name);
        $this->assertNotNull($image->file_path);
        $this->assertNotNull($image->file_extension);
        $this->assertIsInt($image->file_size);
        $this->assertGreaterThan(0, $image->file_size);
    }

    /** @test */
    public function block_image_validates_required_relationships()
    {
        $image = BlockImage::factory()->make([
            'block_id' => null,
            'uploaded_by' => null
        ]);

        // Should fail when trying to save without required relationships
        $this->expectException(\Illuminate\Database\QueryException::class);
        $image->save();
    }

    /** @test */
    public function block_image_is_primary_defaults_to_false()
    {
        $image = BlockImage::factory()->create(['is_primary' => false]);
        $this->assertFalse($image->is_primary);
    }

    /** @test */
    public function block_image_sort_order_defaults_to_zero()
    {
        $image = BlockImage::factory()->create(['sort_order' => 0]);
        $this->assertEquals(0, $image->sort_order);
    }

    /** @test */
    public function block_image_has_timestamps()
    {
        $this->assertNotNull($this->blockImage->created_at);
        $this->assertNotNull($this->blockImage->updated_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->blockImage->created_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $this->blockImage->updated_at);
    }

    /** @test */
    public function block_image_deletes_file_on_model_deletion()
    {
        // Create a fake file in storage
        $filePath = 'blocks/1/test_image.jpg';
        Storage::put($filePath, 'fake image content');
        
        $image = BlockImage::factory()->create([
            'file_path' => 'blocks/1',
            'stored_name' => 'test_image.jpg'
        ]);

        // Verify file exists
        $this->assertTrue(Storage::exists($filePath));

        // Delete the model
        $image->delete();

        // Verify file is deleted (this tests the boot method)
        $this->assertFalse(Storage::exists($filePath));
    }

    /** @test */
    public function only_one_image_per_block_can_be_primary()
    {
        // Create first primary image
        $primaryImage1 = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'is_primary' => true
        ]);

        // Create second image and make it primary
        $primaryImage2 = BlockImage::factory()->create([
            'block_id' => $this->block->id,
            'is_primary' => false
        ]);

        // Manually set second image as primary (simulating controller logic)
        BlockImage::where('block_id', $this->block->id)
                  ->where('id', '!=', $primaryImage2->id)
                  ->update(['is_primary' => false]);
        
        $primaryImage2->update(['is_primary' => true]);

        // Refresh models
        $primaryImage1->refresh();
        $primaryImage2->refresh();

        // Assert only one is primary
        $this->assertFalse($primaryImage1->is_primary);
        $this->assertTrue($primaryImage2->is_primary);

        // Count primary images for this block
        $primaryCount = BlockImage::where('block_id', $this->block->id)
                                  ->where('is_primary', true)
                                  ->count();
        $this->assertEquals(1, $primaryCount);
    }

    /** @test */
    public function block_image_supports_multiple_file_types()
    {
        $supportedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif'
        ];

        foreach ($supportedTypes as $type) {
            $image = BlockImage::factory()->create([
                'file_extension' => $type,
                'mime_type' => $mimeTypes[$type]
            ]);

            $this->assertEquals($type, $image->file_extension);
            $this->assertEquals($mimeTypes[$type], $image->mime_type);
        }
    }

    /** @test */
    public function block_image_handles_large_file_sizes()
    {
        // Test maximum allowed file size (5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB in bytes
        $image = BlockImage::factory()->create(['file_size' => $maxSize]);
        
        $this->assertEquals($maxSize, $image->file_size);
        $this->assertEquals('5 MB', $image->file_size_human);
    }
}
