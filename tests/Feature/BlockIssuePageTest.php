<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use App\Models\User;
use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockWorkOrder;
use App\Models\BlockVisit;
use App\Models\BlockIssueAction;
use App\Models\IssueLog;
use App\Models\Priority;
use App\Models\IssueStatus;
use App\Models\UserType;
use App\Models\BlockContractor;
use Illuminate\Support\Str;

class BlockIssuePageTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    protected $user;
    protected $block;
    protected $blockIssue;
    protected $blockUnit;
    protected $priority;
    protected $issueStatus;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test user
        $this->user = User::factory()->create([
            'email' => 'test_' . uniqid() . '@example.com',
            'user_type_id' => 1,
        ]);

        // Use seeded priorities and statuses
        // Priorities: 1-Low, 2-Normal, 3-High, 4-Urgent, 5-Critical
        // Issue Status: 1-Open, 2-In Progress, 3-Resolved, 4-Closed

        // Create block
        $this->block = Block::factory()->create([
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Create block unit manually
        $this->blockUnit = BlockUnit::create([
            'block_id' => $this->block->id,
            'block_unit_type_id' => 1,
            'unit_code' => 'TEST-UNIT-' . uniqid(),
            'unit_name' => 'Test Unit',
            'salutation' => 'Mr.',
            'owners_name' => 'Test Owner',
            'email' => 'owner_' . uniqid() . '@example.com',
            'resident' => 1,
            'country_id' => 1,
            'state_id' => 1,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Create block issue
        $this->blockIssue = BlockIssue::create([
            'block_id' => $this->block->id,
            'block_unit_id' => $this->blockUnit->id,
            'ref_no' => 'ISS-TEST-' . uniqid(),
            'issue' => 'Test Issue',
            'issue_details' => 'Test issue description',
            'priority_id' => 2, // Normal
            'issue_status_id' => 1, // Open
            'reported_by' => $this->user->id,
            'assigned_to' => $this->user->id,
            'issued_by' => $this->user->id,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Authenticate the user
        $this->actingAs($this->user);
    }

    /** @test */
    public function test_can_view_issue_page()
    {
        $response = $this->get(route('block-issues.show', $this->blockIssue));

        $response->assertStatus(200);
        $response->assertSee($this->blockIssue->ref_no);
        $response->assertSee($this->blockIssue->issue);
        $response->assertSee('Raise Work Order');
        $response->assertSee('Assign Site Visit');
        $response->assertSee('Add Action');
    }

    /** @test */
    public function test_can_raise_work_order_with_default_pending_status()
    {
        // Create a contractor
        $contractor = User::factory()->create([
            'email' => 'contractor_' . uniqid() . '@example.com',
            'user_type_id' => 1,
        ]);

        $workOrderData = [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'block_unit_id' => $this->blockUnit->id,
            'priority_id' => 2,
            'contractor_id' => $contractor->id,
            'status' => 1, // Pending
            'comment' => 'Test work order comment',
        ];

        $response = $this->postJson(route('block-work-orders.store'), $workOrderData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert work order was created with pending status
        $this->assertDatabaseHas('block_work_orders', [
            'block_issue_id' => $this->blockIssue->id,
            'contractor_id' => $contractor->id,
            'status' => 1, // Pending
        ]);
    }

    /** @test */
    public function test_work_order_creation_logs_in_issue_logs()
    {
        $contractor = User::factory()->create([
            'email' => 'contractor_' . uniqid() . '@example.com',
            'user_type_id' => 1,
        ]);

        $workOrderData = [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'priority_id' => 2,
            'contractor_id' => $contractor->id,
            'status' => 1,
            'comment' => 'Test work order',
        ];

        $this->postJson(route('block-work-orders.store'), $workOrderData);

        // Assert issue log was created
        $this->assertDatabaseHas('issue_logs', [
            'block_issue_id' => $this->blockIssue->id,
            'log_type' => 'work_order_created',
            'user_id' => $this->user->id,
        ]);

        $log = IssueLog::where('block_issue_id', $this->blockIssue->id)
            ->where('log_type', 'work_order_created')
            ->first();

        $this->assertNotNull($log);
        $this->assertStringContainsString('Work order', $log->description);
    }

    /** @test */
    public function test_can_assign_site_visit()
    {
        // Create contractor admin user
        $contractorAdminType = UserType::firstOrCreate(
            ['name' => 'Contractor Admin'],
            ['name' => 'Contractor Admin', 'slug' => 'contractor-admin']
        );

        $contractorAdmin = User::factory()->create([
            'email' => 'contractor_admin_' . uniqid() . '@example.com',
            'user_type_id' => $contractorAdminType->id,
        ]);

        $siteVisitData = [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'block_unit_id' => $this->blockUnit->id,
            'assigned_user_id' => $contractorAdmin->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test site visit',
        ];

        $response = $this->postJson(route('block-visits.store'), $siteVisitData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert site visit was created
        $this->assertDatabaseHas('block_visits', [
            'block_issue_id' => $this->blockIssue->id,
        ]);
    }

    /** @test */
    public function test_site_visit_creation_logs_in_issue_logs()
    {
        $contractorAdminType = UserType::firstOrCreate(
            ['name' => 'Contractor Admin'],
            ['name' => 'Contractor Admin', 'slug' => 'contractor-admin']
        );

        $contractorAdmin = User::factory()->create([
            'email' => 'contractor_admin_' . uniqid() . '@example.com',
            'user_type_id' => $contractorAdminType->id,
        ]);

        $siteVisitData = [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'block_unit_id' => $this->blockUnit->id,
            'assigned_user_id' => $contractorAdmin->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $this->postJson(route('block-visits.store'), $siteVisitData);

        // Assert issue log was created
        $this->assertDatabaseHas('issue_logs', [
            'block_issue_id' => $this->blockIssue->id,
            'log_type' => 'site_visit_assigned',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function test_issue_page_does_not_duplicate_issue_specific_site_visits()
    {
        // Create a site visit linked directly to this issue
        $issueSpecificVisit = BlockVisit::create([
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'block_unit_id' => $this->blockUnit->id,
            'ref_no' => 'SV-' . Str::upper(Str::random(6)),
            'scheduled_date_time' => now()->addDay(),
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        // Create a general block site visit (not tied to the current issue)
        $generalVisit = BlockVisit::create([
            'block_id' => $this->block->id,
            'block_issue_id' => null,
            'block_unit_id' => $this->blockUnit->id,
            'ref_no' => 'SV-' . Str::upper(Str::random(6)),
            'scheduled_date_time' => now()->addDays(2),
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->get(route('block-issues.show', $this->blockIssue));

        $response->assertStatus(200);

        $siteVisits = $response->viewData('siteVisits');
        $relatedSiteVisits = $response->viewData('relatedSiteVisits');

        $this->assertTrue(
            $relatedSiteVisits->contains('id', $issueSpecificVisit->id),
            'Issue-linked visit should appear in relatedSiteVisits collection'
        );

        $this->assertFalse(
            $siteVisits->contains('id', $issueSpecificVisit->id),
            'Issue-linked visit should not appear in general siteVisits collection'
        );

        $this->assertTrue(
            $siteVisits->contains('id', $generalVisit->id),
            'General block visit should remain visible in siteVisits collection'
        );
    }

    /** @test */
    public function test_can_add_action_with_default_pending_status()
    {
        $actionData = [
            'block_issue_id' => $this->blockIssue->id,
            'action_type' => 'site_visit',
            'description' => 'Test action description',
            'status' => 'pending', // Default status
            'action_date' => now()->format('Y-m-d H:i:s'),
            'priority' => 'normal',
            'performed_by' => $this->user->id,
            'notes' => 'Test notes',
        ];

        $response = $this->postJson(route('block-issues.store-action', $this->blockIssue->id), $actionData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Assert action was created with pending status
        $this->assertDatabaseHas('block_issue_actions', [
            'block_issue_id' => $this->blockIssue->id,
            'action_type' => 'site_visit',
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function test_action_does_not_require_cost()
    {
        $actionData = [
            'block_issue_id' => $this->blockIssue->id,
            'action_type' => 'minor_repairs',
            'description' => 'Test repair action',
            'status' => 'pending',
            'action_date' => now()->format('Y-m-d H:i:s'),
            'performed_by' => $this->user->id,
            // No cost field
        ];

        $response = $this->postJson(route('block-issues.store-action', $this->blockIssue->id), $actionData);

        $response->assertStatus(200);

        // Assert action was created without cost
        $this->assertDatabaseHas('block_issue_actions', [
            'block_issue_id' => $this->blockIssue->id,
            'action_type' => 'minor_repairs',
            'cost' => null,
        ]);
    }

    /** @test */
    public function test_work_order_shows_assigned_contractor_name()
    {
        $contractor = User::factory()->create([
            'name' => 'John Contractor',
            'email' => 'john_contractor_' . uniqid() . '@example.com',
            'user_type_id' => 1,
        ]);

        $workOrder = BlockWorkOrder::create([
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'ref_no' => 'WO-' . uniqid(),
            'priority_id' => 2,
            'contractor_id' => $contractor->id,
            'status' => 1,
            'issued_by' => $this->user->id,
            'created_by' => $this->user->id,
            'updated_by' => $this->user->id,
        ]);

        $response = $this->get(route('block-issues.show', $this->blockIssue));

        $response->assertStatus(200);
        $response->assertSee('John Contractor');
        $response->assertSee($contractor->email);
    }

    /** @test */
    public function test_issue_logs_are_tracked_properly()
    {
        // Create an issue log
        IssueLog::createLog(
            $this->blockIssue->id,
            'work_order_created',
            'Work order WO-123 raised',
            ['user_id' => $this->user->id]
        );

        // Assert log was created
        $this->assertDatabaseHas('issue_logs', [
            'block_issue_id' => $this->blockIssue->id,
            'log_type' => 'work_order_created',
            'description' => 'Work order WO-123 raised',
            'user_id' => $this->user->id,
        ]);

        // Test relationship
        $logs = $this->blockIssue->logs;
        $this->assertCount(1, $logs);
        $this->assertEquals('work_order_created', $logs->first()->log_type);
    }

    /** @test */
    public function test_site_visit_supports_file_uploads()
    {
        Storage::fake('public');

        $contractorAdminType = UserType::firstOrCreate(
            ['name' => 'Contractor Admin'],
            ['name' => 'Contractor Admin', 'slug' => 'contractor-admin']
        );

        $contractorAdmin = User::factory()->create([
            'email' => 'contractor_admin_' . uniqid() . '@example.com',
            'user_type_id' => $contractorAdminType->id,
        ]);

        $file = UploadedFile::fake()->image('test-image.jpg');

        $siteVisitData = [
            'block_id' => $this->block->id,
            'block_issue_id' => $this->blockIssue->id,
            'block_unit_id' => $this->blockUnit->id,
            'assigned_user_id' => $contractorAdmin->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'files' => [$file],
        ];

        $response = $this->postJson(route('block-visits.store'), $siteVisitData);

        $response->assertStatus(200);
    }
}
