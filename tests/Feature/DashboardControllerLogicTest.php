<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\BlockUnit;
use App\Models\BlockIssue;
use App\Models\BlockWorkOrder;
use App\Models\Priority;
use App\Models\IssueStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DashboardControllerLogicTest extends TestCase
{
    use RefreshDatabase;

    protected $adminType;
    protected $contractorAdminType;
    protected $contractorUserType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types
        $this->adminType = UserType::factory()->admin()->create();
        $this->contractorAdminType = UserType::factory()->contractorAdmin()->create();
        $this->contractorUserType = UserType::factory()->contractorUser()->create();
    }

    /** @test */
    public function admin_dashboard_returns_correct_data_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create test data
        $blockType = BlockType::factory()->create();
        $block = Block::factory()->create(['block_type_id' => $blockType->id]);
        $blockUnit = BlockUnit::factory()->create(['block_id' => $block->id]);
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        // Create issues with different statuses
        BlockIssue::factory()->withStatus(1)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Open
        BlockIssue::factory()->withStatus(2)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // In Progress
        BlockIssue::factory()->withStatus(3)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Resolved
        
        $this->actingAs($admin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('isContractorAdmin', false);
        
        $stats = $response->viewData('stats');
        $this->assertEquals(1, $stats['total_blocks']);
        $this->assertEquals(1, $stats['total_block_types']);
        $this->assertEquals(1, $stats['total_units']);
        $this->assertEquals(3, $stats['total_issues']);
    }

    /** @test */
    public function contractor_admin_dashboard_returns_contractor_specific_data()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create contractor users created by this admin
        $contractorUser1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $contractorUser2 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        // Create work orders assigned to this contractor admin
        $workOrder1 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->pending()->create();
        $workOrder2 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->inProgress()->create();
        $workOrder3 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->completed()->create();
        
        // Create work orders assigned to other contractors (should not be counted)
        $otherWorkOrder = BlockWorkOrder::factory()->assignedTo($contractorUser1->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('stats');
        $response->assertViewHas('isContractorAdmin', true);
        
        $stats = $response->viewData('stats');
        $this->assertEquals(2, $stats['total_contractor_users']); // Only contractor users created by this admin
        $this->assertEquals(4, $stats['total_work_orders']); // All work orders
        $this->assertEquals(3, $stats['assigned_work_orders']); // Only work orders assigned to this admin
        $this->assertEquals(1, $stats['completed_work_orders']); // Only completed work orders assigned to this admin
    }

    /** @test */
    public function contractor_admin_dashboard_shows_recent_contractor_users()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create contractor users created by this admin
        $contractorUser1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $contractorUser2 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $contractorUser3 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        // Create contractor user created by different admin (should not appear)
        $otherAdmin = User::factory()->admin()->create();
        $otherContractorUser = User::factory()->contractorUser()->createdBy($otherAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('recentContractorUsers');
        
        $recentUsers = $response->viewData('recentContractorUsers');
        $this->assertCount(3, $recentUsers);
        
        // Check that only users created by this admin are included
        $userIds = $recentUsers->pluck('id')->toArray();
        $this->assertContains($contractorUser1->id, $userIds);
        $this->assertContains($contractorUser2->id, $userIds);
        $this->assertContains($contractorUser3->id, $userIds);
        $this->assertNotContains($otherContractorUser->id, $userIds);
    }

    /** @test */
    public function contractor_admin_dashboard_shows_recent_work_orders()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create work orders assigned to this contractor admin
        $workOrder1 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->create();
        $workOrder2 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->create();
        $workOrder3 = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->create();
        
        // Create work order assigned to different contractor (should not appear)
        $otherContractor = User::factory()->contractorUser()->create();
        $otherWorkOrder = BlockWorkOrder::factory()->assignedTo($otherContractor->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('recentWorkOrders');
        
        $recentWorkOrders = $response->viewData('recentWorkOrders');
        $this->assertCount(3, $recentWorkOrders);
        
        // Check that only work orders assigned to this admin are included
        $workOrderIds = $recentWorkOrders->pluck('id')->toArray();
        $this->assertContains($workOrder1->id, $workOrderIds);
        $this->assertContains($workOrder2->id, $workOrderIds);
        $this->assertContains($workOrder3->id, $workOrderIds);
        $this->assertNotContains($otherWorkOrder->id, $workOrderIds);
    }

    /** @test */
    public function admin_dashboard_shows_recent_blocks()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create blocks
        $blockType = BlockType::factory()->create();
        $block1 = Block::factory()->create(['block_type_id' => $blockType->id]);
        $block2 = Block::factory()->create(['block_type_id' => $blockType->id]);
        $block3 = Block::factory()->create(['block_type_id' => $blockType->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('recentBlocks');
        
        $recentBlocks = $response->viewData('recentBlocks');
        $this->assertCount(3, $recentBlocks);
    }

    /** @test */
    public function admin_dashboard_shows_recent_issues()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create issues
        $block = Block::factory()->create();
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        $issue1 = BlockIssue::factory()->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        $issue2 = BlockIssue::factory()->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        $issue3 = BlockIssue::factory()->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('recentIssues');
        
        $recentIssues = $response->viewData('recentIssues');
        $this->assertCount(3, $recentIssues);
    }

    /** @test */
    public function issues_stats_api_returns_correct_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create issues with different statuses
        $block = Block::factory()->create();
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        BlockIssue::factory()->withStatus(1)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Open
        BlockIssue::factory()->withStatus(2)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // In Progress
        BlockIssue::factory()->withStatus(3)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Resolved
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'by_status',
            'by_priority',
            'by_block',
            'trend'
        ]);
        
        $data = $response->json();
        $this->assertArrayHasKey('Open', $data['by_status']);
        $this->assertArrayHasKey('In Progress', $data['by_status']);
        $this->assertArrayHasKey('Resolved', $data['by_status']);
    }

    /** @test */
    public function dashboard_stats_api_returns_correct_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create test data
        $blockType = BlockType::factory()->create();
        $block = Block::factory()->create(['block_type_id' => $blockType->id]);
        $blockUnit = BlockUnit::factory()->create(['block_id' => $block->id]);
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        BlockIssue::factory()->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/stats');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'total_blocks',
            'total_block_types',
            'total_units',
            'total_issues',
            'open_issues',
            'in_progress_issues',
            'resolved_issues',
            'high_priority_issues'
        ]);
        
        $data = $response->json();
        $this->assertEquals(1, $data['total_blocks']);
        $this->assertEquals(1, $data['total_block_types']);
        $this->assertEquals(1, $data['total_units']);
        $this->assertEquals(1, $data['total_issues']);
    }

    /** @test */
    public function recent_issues_api_returns_correct_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create issues
        $block = Block::factory()->create();
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        $issue = BlockIssue::factory()->create([
            'block_id' => $block->id,
            'priority_id' => $priority->id,
            'issue_status_id' => $issueStatus->id,
            'ref_no' => 'ISS-001',
            'issue' => 'Test Issue'
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-issues');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'ref_no',
                'issue',
                'block_name',
                'status',
                'status_color',
                'priority',
                'priority_color',
                'created_at'
            ]
        ]);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('ISS-001', $data[0]['ref_no']);
        $this->assertEquals('Test Issue', $data[0]['issue']);
    }

    /** @test */
    public function recent_blocks_api_returns_correct_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create blocks
        $blockType = BlockType::factory()->create(['name' => 'Residential']);
        $block = Block::factory()->create([
            'block_type_id' => $blockType->id,
            'name' => 'Test Block'
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-blocks');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            '*' => [
                'id',
                'name',
                'type',
                'created_at'
            ]
        ]);
        
        $data = $response->json();
        $this->assertCount(1, $data);
        $this->assertEquals('Test Block', $data[0]['name']);
        $this->assertEquals('Residential', $data[0]['type']);
    }

    /** @test */
    public function dashboard_requires_authentication()
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function dashboard_api_endpoints_require_authentication()
    {
        $endpoints = [
            '/api/dashboard/issues-stats',
            '/api/dashboard/stats',
            '/api/dashboard/recent-issues',
            '/api/dashboard/recent-blocks'
        ];
        
        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $response->assertRedirect('/login');
        }
    }

    /** @test */
    public function contractor_admin_cannot_see_regular_admin_data()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create data that should not be visible to contractor admin
        $blockType = BlockType::factory()->create();
        $block = Block::factory()->create(['block_type_id' => $blockType->id]);
        $blockUnit = BlockUnit::factory()->create(['block_id' => $block->id]);
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        $issue = BlockIssue::factory()->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $response->assertViewHas('isContractorAdmin', true);
        
        // Should not have regular admin data
        $this->assertFalse($response->viewData()->has('recentBlocks'));
        $this->assertFalse($response->viewData()->has('recentIssues'));
        
        // Should have contractor admin data
        $this->assertTrue($response->viewData()->has('recentContractorUsers'));
        $this->assertTrue($response->viewData()->has('recentWorkOrders'));
    }

    /** @test */
    public function dashboard_statistics_are_accurate()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create test data with known counts
        $blockType = BlockType::factory()->create();
        $block = Block::factory()->create(['block_type_id' => $blockType->id]);
        $blockUnit = BlockUnit::factory()->create(['block_id' => $block->id]);
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        // Create 5 issues with different statuses
        BlockIssue::factory()->withStatus(1)->count(2)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Open
        BlockIssue::factory()->withStatus(2)->count(2)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // In Progress
        BlockIssue::factory()->withStatus(3)->count(1)->create(['block_id' => $block->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]); // Resolved
        
        $this->actingAs($admin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        
        $this->assertEquals(1, $stats['total_blocks']);
        $this->assertEquals(1, $stats['total_block_types']);
        $this->assertEquals(1, $stats['total_units']);
        $this->assertEquals(5, $stats['total_issues']);
        $this->assertEquals(2, $stats['open_issues']);
        $this->assertEquals(2, $stats['in_progress_issues']);
        $this->assertEquals(1, $stats['resolved_issues']);
    }

    /** @test */
    public function dashboard_role_detection_works_correctly()
    {
        $admin = User::factory()->admin()->create();
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Test admin user
        $this->actingAs($admin);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('isContractorAdmin', false);
        
        // Test contractor admin user
        $this->actingAs($contractorAdmin);
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewHas('isContractorAdmin', true);
    }

    /** @test */
    public function dashboard_work_order_status_calculation_is_correct()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create work orders with different statuses
        $pendingWorkOrder = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->pending()->create();
        $inProgressWorkOrder = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->inProgress()->create();
        $completedWorkOrder = BlockWorkOrder::factory()->assignedTo($contractorAdmin->id)->completed()->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/dashboard');
        
        $response->assertStatus(200);
        $stats = $response->viewData('stats');
        
        $this->assertEquals(3, $stats['assigned_work_orders']); // All assigned work orders
        $this->assertEquals(1, $stats['completed_work_orders']); // Only completed work orders
    }
}
