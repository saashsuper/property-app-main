<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockVisit;
use App\Models\BlockVisitImage;
use App\Models\JobReason;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BlockVisitCreationTest extends TestCase
{
    use DatabaseTransactions, WithFaker; // Changed from DatabaseTransactions to DatabaseTransactions

    protected $user;
    protected $block;
    protected $blockIssue;
    protected $jobReason;
    protected $contractorUser;

    /**
     * Setup test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Get existing user types (seeded)
        $adminType = UserType::where('name', 'Admin')->first();
        $contractorType = UserType::where('name', 'Contractor Admin')->first();
        
        // If not found in test database, they might not be seeded yet
        if (!$adminType || !$contractorType) {
            $this->markTestSkipped('User types not seeded in test database. Run: DB_DATABASE=proman_test php artisan migrate:fresh --seed');
        }

        // Create users with unique emails for each test
        $this->user = User::factory()->create([
            'user_type_id' => $adminType->id,
            'email' => 'admin_' . uniqid() . '@test.com',
        ]);

        $this->contractorUser = User::factory()->create([
            'user_type_id' => $contractorType->id,
            'email' => 'contractor_' . uniqid() . '@test.com',
        ]);

        // Create block
        $this->block = Block::factory()->create([
            'name' => 'Test Block',
            'management_company' => 'Test Management',
        ]);

        // Create block issue manually
        $this->blockIssue = BlockIssue::create([
            'block_id' => $this->block->id,
            'ref_no' => 'ISS-TEST001',
            'priority_id' => 1,
            'issue_status_id' => 1,
            'reported_by' => $this->user->id,
            'issued_from' => 1,
            'from_id' => $this->user->id,
            'issued_by' => $this->user->id,
            'issue' => 'Test Issue',
            'issue_details' => 'Test issue details',
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Get existing job reason (seeded)
        $this->jobReason = JobReason::first();
        if (!$this->jobReason) {
            $this->markTestSkipped('Job reasons not seeded in test database.');
        }

        // Setup storage fake
        Storage::fake('public');
    }

    /**
     * Test: Create site visit without files
     */
    public function test_can_create_site_visit_without_files()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test site visit',
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Site visit scheduled successfully',
                 ]);

        $this->assertDatabaseHas('block_visits', [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'notes' => 'Test site visit',
        ]);

        // Verify team member was created
        $blockVisit = BlockVisit::latest()->first();
        $this->assertDatabaseHas('block_visit_teams', [
            'block_visit_id' => $blockVisit->id,
            'user_id' => $this->contractorUser->id,
            'leed' => true,
        ]);
    }

    /**
     * Test: Create site visit with single image file
     */
    public function test_can_create_site_visit_with_single_image()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('test-image.jpg', 800, 600)->size(1024); // 1MB

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test site visit with image',
            'files' => [$file],
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $blockVisit = BlockVisit::latest()->first();

        // Verify image record was created
        $this->assertDatabaseHas('block_visit_images', [
            'block_visit_id' => $blockVisit->id,
            'image_path' => 'block-visits/' . $blockVisit->id,
        ]);

        // Verify file was saved to storage
        $image = BlockVisitImage::where('block_visit_id', $blockVisit->id)->first();
        $this->assertNotNull($image);
        $this->assertNotNull($image->image_name);
        
        Storage::disk('public')->assertExists('block-visits/' . $blockVisit->id . '/' . $image->image_name);
    }

    /**
     * Test: Create site visit with multiple files (images and documents)
     */
    public function test_can_create_site_visit_with_multiple_files()
    {
        $this->actingAs($this->user);

        $imageFile = UploadedFile::fake()->image('test-photo.jpg', 800, 600)->size(1024);
        $pdfFile = UploadedFile::fake()->create('test-document.pdf', 2048, 'application/pdf');

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test site visit with multiple files',
            'files' => [$imageFile, $pdfFile],
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                 ]);

        $blockVisit = BlockVisit::latest()->first();

        // Verify both files were saved
        $this->assertEquals(2, $blockVisit->images()->count());

        // Verify files exist in storage
        $images = BlockVisitImage::where('block_visit_id', $blockVisit->id)->get();
        foreach ($images as $image) {
            Storage::disk('public')->assertExists('block-visits/' . $blockVisit->id . '/' . $image->image_name);
        }
    }

    /**
     * Test: File validation - file too large
     */
    public function test_rejects_file_larger_than_5mb()
    {
        $this->actingAs($this->user);

        $largeFile = UploadedFile::fake()->image('large-image.jpg')->size(6000); // 6MB

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test with large file',
            'files' => [$largeFile],
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed',
                 ]);
    }

    /**
     * Test: File validation - invalid file type
     */
    public function test_rejects_invalid_file_type()
    {
        $this->actingAs($this->user);

        $invalidFile = UploadedFile::fake()->create('test.exe', 1024, 'application/x-msdownload');

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test with invalid file',
            'files' => [$invalidFile],
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed',
                 ]);
    }

    /**
     * Test: Verify response includes images data
     */
    public function test_response_includes_images_data()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('test-image.jpg')->size(1024);

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test site visit',
            'files' => [$file],
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'ref_no',
                         'images' => [
                             '*' => [
                                 'id',
                                 'block_visit_id',
                                 'image_path',
                                 'image_name',
                             ]
                         ]
                     ]
                 ]);

        $responseData = $response->json('data');
        $this->assertCount(1, $responseData['images']);
    }

    /**
     * Test: Required fields validation
     */
    public function test_validates_required_fields()
    {
        $this->actingAs($this->user);

        $response = $this->postJson(route('block-visits.store'), [
            // Missing required fields
        ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Validation failed',
                 ])
                 ->assertJsonValidationErrors(['block_id', 'user_id', 'scheduled_date_time', 'job_reason_id']);
    }

    /**
     * Test: Verify file naming convention
     */
    public function test_files_have_correct_naming_convention()
    {
        $this->actingAs($this->user);

        $file = UploadedFile::fake()->image('original-name.jpg')->size(1024);

        $response = $this->postJson(route('block-visits.store'), [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'user_id' => $this->contractorUser->id,
            'job_reason_id' => $this->jobReason->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test naming',
            'files' => [$file],
        ]);

        $response->assertStatus(200);

        $blockVisit = BlockVisit::latest()->first();
        $image = BlockVisitImage::where('block_visit_id', $blockVisit->id)->first();

        // Verify naming format: timestamp_randomstring_originalname
        $this->assertMatchesRegularExpression('/^\d{14}_[a-zA-Z0-9]{8}_original-name\.jpg$/', $image->image_name);
    }
}
