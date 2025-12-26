<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use App\Models\BlockWorkOrder;
use App\Models\BlockWorkOrderLog;
use App\Models\BlockWorkOrderImage;
use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockBuilding;
use App\Models\Priority;
use App\Models\JobStatus;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class BlockWorkOrderLoggingTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $block;
    protected $blockIssue;
    protected $priority;
    protected $jobStatuses;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $adminType = UserType::factory()->admin()->create();
        $this->admin = User::factory()->withUserType($adminType->id)->create();
        
        // Create block
        $this->block = Block::factory()->create();
        
        // Create building and unit
        $building = BlockBuilding::factory()->create(['block_id' => $this->block->id]);
        $unit = BlockUnit::factory()->create([
            'block_id' => $this->block->id,
            'block_building_id' => $building->id,
        ]);
        
        // Create priority
        $this->priority = Priority::factory()->normal()->create();
        
        // Create block issue
        $this->blockIssue = BlockIssue::factory()->create([
            'block_id' => $this->block->id,
            'block_unit_id' => $unit->id,
            'block_building_id' => $building->id,
            'priority_id' => $this->priority->id,
        ]);
        
        // Create job statuses
        $this->jobStatuses = [
            'pending' => JobStatus::firstOrCreate(['name' => 'Scheduled'], ['is_updated' => 1]),
            'in_progress' => JobStatus::firstOrCreate(['name' => 'In Progress'], ['is_updated' => 1]),
            'completed' => JobStatus::firstOrCreate(['name' => 'Completed'], ['is_updated' => 1]),
            'on_hold' => JobStatus::firstOrCreate(['name' => 'On Hold'], ['is_updated' => 1]),
        ];
        
        // Fake storage for file uploads
        Storage::fake('public');
    }

    /** @test */
    public function work_order_creation_logs_created_event()
    {
        $this->actingAs($this->admin);
        
        $workOrderData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['pending']->id,
        ];
        
        $response = $this->post('/block-work-orders', $workOrderData);
        
        $workOrder = BlockWorkOrder::where('block_issue_id', $this->blockIssue->id)->first();
        $this->assertNotNull($workOrder);
        
        // Check that a log entry was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'created',
            'description' => 'Work order created',
            'user_id' => $this->admin->id,
        ]);
        
        // Verify log can be accessed via relationship
        $this->assertCount(1, $workOrder->logs);
        $this->assertEquals('created', $workOrder->logs->first()->log_type);
    }

    /** @test */
    public function work_order_creation_with_images_logs_attachment_added()
    {
        $this->actingAs($this->admin);
        
        $image1 = UploadedFile::fake()->image('test1.jpg', 100, 100);
        $image2 = UploadedFile::fake()->image('test2.jpg', 100, 100);
        
        $workOrderData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['pending']->id,
            'images' => [$image1, $image2],
        ];
        
        $response = $this->post('/block-work-orders', $workOrderData);
        
        $workOrder = BlockWorkOrder::where('block_issue_id', $this->blockIssue->id)->first();
        
        // Check that attachment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'attachment_added',
            'description' => '2 photo(s) uploaded during creation',
            'field_name' => 'images',
            'new_value' => '2',
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function work_order_status_change_logs_status_changed_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['pending']->id,
        ]);
        
        // Update status to In Progress
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        // Check that status change log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'status_changed',
            'field_name' => 'status',
            'user_id' => $this->admin->id,
        ]);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'status_changed')
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('Status changed from', $log->description);
    }

    /** @test */
    public function work_order_priority_change_logs_priority_changed_event()
    {
        $this->actingAs($this->admin);
        
        $priorityHigh = Priority::factory()->high()->create();
        $priorityUrgent = Priority::factory()->urgent()->create();
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $priorityHigh->id,
        ]);
        
        // Update priority
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $priorityUrgent->id,
            'status' => $workOrder->status,
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        // Check that priority change log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'priority_changed',
            'field_name' => 'priority_id',
            'user_id' => $this->admin->id,
        ]);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'priority_changed')
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('Priority changed from', $log->description);
    }

    /** @test */
    public function work_order_comment_update_logs_comment_added_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'comment' => 'Original comment',
        ]);
        
        // Update comment
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $workOrder->status,
            'comment' => 'Updated comment',
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        // Check that comment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'comment_added',
            'field_name' => 'comment',
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function work_order_photo_upload_logs_attachment_added_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
        ]);
        
        $image = UploadedFile::fake()->image('new_photo.jpg', 100, 100);
        
        // Update with new image
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $workOrder->status,
            'images' => [$image],
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        // Check that attachment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'attachment_added',
            'field_name' => 'images',
            'user_id' => $this->admin->id,
        ]);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'attachment_added')
            ->latest()
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('photo(s) uploaded', $log->description);
    }

    /** @test */
    public function api_pause_work_order_logs_paused_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $response = $this->postJson("/api/block-work-orders/{$workOrder->id}/pause", [
            'reason' => 'Waiting for parts',
        ]);
        
        $response->assertStatus(200);
        
        // Check that paused log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'paused',
            'field_name' => 'status',
            'user_id' => $this->admin->id,
        ]);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'paused')
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('paused', $log->description);
        $this->assertStringContainsString('Waiting for parts', $log->description);
    }

    /** @test */
    public function api_resume_work_order_logs_resumed_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['on_hold']->id,
        ]);
        
        $response = $this->postJson("/api/block-work-orders/{$workOrder->id}/resume");
        
        $response->assertStatus(200);
        
        // Check that resumed log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'resumed',
            'field_name' => 'status',
            'user_id' => $this->admin->id,
        ]);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'resumed')
            ->first();
        
        $this->assertNotNull($log);
        $this->assertStringContainsString('resumed', $log->description);
    }

    /** @test */
    public function api_complete_work_order_logs_completed_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $response = $this->postJson("/api/block-work-orders/{$workOrder->id}/complete", [
            'comment' => 'Job completed successfully',
        ]);
        
        $response->assertStatus(200);
        
        // Check that completed log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'completed',
            'field_name' => 'status',
            'user_id' => $this->admin->id,
        ]);
        
        // Check that comment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'comment_added',
            'field_name' => 'comment',
            'user_id' => $this->admin->id,
        ]);
        
        // Check that work docket generated log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'work_docket_generated',
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function api_photo_upload_logs_attachment_added_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
        ]);
        
        $image = UploadedFile::fake()->image('photo.jpg', 100, 100);
        
        $response = $this->postJson("/api/block-work-orders/{$workOrder->id}/photos", [
            'photos' => [$image],
        ]);
        
        $response->assertStatus(200);
        
        // Check that attachment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'attachment_added',
            'field_name' => 'images',
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function api_photo_deletion_logs_attachment_deleted_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $photo = BlockWorkOrderImage::create([
            'block_work_order_id' => $workOrder->id,
            'image_name' => 'test_image.jpg',
            'image_path' => 'work-orders/images',
            's3_status' => 0,
            'created_by' => $this->admin->id,
        ]);
        
        $response = $this->deleteJson("/api/block-work-orders/{$workOrder->id}/photos/{$photo->id}");
        
        $response->assertStatus(200);
        
        // Check that attachment deleted log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'attachment_deleted',
            'field_name' => 'images',
            'related_id' => $photo->id,
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function api_note_addition_logs_comment_added_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $response = $this->postJson("/api/block-work-orders/{$workOrder->id}/notes", [
            'note' => 'This is a test note',
        ]);
        
        $response->assertStatus(200);
        
        // Check that comment log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'comment_added',
            'field_name' => 'notes',
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function api_note_deletion_logs_comment_updated_event()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $note = \App\Models\BlockWorkOrderNote::create([
            'block_work_order_id' => $workOrder->id,
            'note' => 'Test note to be deleted',
            'note_type' => 'note',
            'created_by' => $this->admin->id,
        ]);
        
        $response = $this->deleteJson("/api/block-work-orders/{$workOrder->id}/notes/{$note->id}");
        
        $response->assertStatus(200);
        
        // Check that comment updated log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'comment_updated',
            'field_name' => 'notes',
            'related_id' => $note->id,
            'user_id' => $this->admin->id,
        ]);
    }

    /** @test */
    public function work_order_logs_include_user_information()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
        ]);
        
        // Update status
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        $log = BlockWorkOrderLog::where('block_work_order_id', $workOrder->id)
            ->where('log_type', 'status_changed')
            ->first();
        
        $this->assertNotNull($log);
        $this->assertEquals($this->admin->id, $log->user_id);
        $this->assertNotNull($log->user);
        $this->assertEquals($this->admin->id, $log->user->id);
    }

    /** @test */
    public function work_order_logs_are_accessible_via_relationship()
    {
        $this->actingAs($this->admin);
        
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
        ]);
        
        // Create multiple logs by updating different fields
        $updateData1 = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ];
        $this->put("/block-work-orders/{$workOrder->id}", $updateData1);
        
        $priorityHigh = Priority::factory()->high()->create();
        $updateData2 = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $priorityHigh->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ];
        $this->put("/block-work-orders/{$workOrder->id}", $updateData2);
        
        // Reload work order with logs
        $workOrder->refresh();
        $workOrder->load('logs.user');
        
        // Verify logs are accessible
        $this->assertGreaterThanOrEqual(2, $workOrder->logs->count());
        
        // Verify logs have user relationship loaded
        foreach ($workOrder->logs as $log) {
            $this->assertNotNull($log->user);
        }
    }

    /** @test */
    public function work_order_regenerate_docket_logs_regenerated_event()
    {
        $this->actingAs($this->admin);
        
        // First complete the work order
        $workOrder = BlockWorkOrder::factory()->create([
            'block_issue_id' => $this->blockIssue->id,
            'status' => $this->jobStatuses['in_progress']->id,
        ]);
        
        $this->postJson("/api/block-work-orders/{$workOrder->id}/complete");
        
        // Now regenerate the docket
        $updateData = [
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => $this->priority->id,
            'status' => $this->jobStatuses['completed']->id,
            'comment' => 'Updated comment before regeneration',
            'regenerate_docket' => '1',
        ];
        
        $response = $this->put("/block-work-orders/{$workOrder->id}", $updateData);
        
        // Check that regenerated log was created
        $this->assertDatabaseHas('block_work_order_logs', [
            'block_work_order_id' => $workOrder->id,
            'log_type' => 'work_docket_regenerated',
            'user_id' => $this->admin->id,
        ]);
    }
}

