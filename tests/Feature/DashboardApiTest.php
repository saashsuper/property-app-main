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
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DashboardApiTest extends TestCase
{
    use DatabaseTransactions;

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
        
        $response = $this->get('/api/dashboard/stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertEquals(1, $data['total_blocks']);
        $this->assertEquals(1, $data['total_block_types']);
        $this->assertEquals(1, $data['total_units']);
        $this->assertEquals(5, $data['total_issues']);
        $this->assertEquals(2, $data['open_issues']);
        $this->assertEquals(2, $data['in_progress_issues']);
        $this->assertEquals(1, $data['resolved_issues']);
    }

    /** @test */
    public function issues_stats_api_returns_trend_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertArrayHasKey('trend', $data);
        $this->assertArrayHasKey('dates', $data['trend']);
        $this->assertArrayHasKey('Open', $data['trend']);
        $this->assertArrayHasKey('In Progress', $data['trend']);
        $this->assertArrayHasKey('Resolved', $data['trend']);
        $this->assertArrayHasKey('Closed', $data['trend']);
        
        // Should have 30 days of data
        $this->assertCount(30, $data['trend']['dates']);
        $this->assertCount(30, $data['trend']['Open']);
        $this->assertCount(30, $data['trend']['In Progress']);
        $this->assertCount(30, $data['trend']['Resolved']);
        $this->assertCount(30, $data['trend']['Closed']);
    }

    /** @test */
    public function issues_stats_api_returns_priority_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create issues with different priorities
        $block = Block::factory()->create();
        $priority1 = Priority::factory()->create();
        $priority2 = Priority::factory()->create();
        $priority3 = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        BlockIssue::factory()->withPriority($priority1->id)->create(['block_id' => $block->id, 'issue_status_id' => $issueStatus->id]);
        BlockIssue::factory()->withPriority($priority2->id)->create(['block_id' => $block->id, 'issue_status_id' => $issueStatus->id]);
        BlockIssue::factory()->withPriority($priority3->id)->create(['block_id' => $block->id, 'issue_status_id' => $issueStatus->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertArrayHasKey('by_priority', $data);
        $this->assertArrayHasKey('Low', $data['by_priority']);
        $this->assertArrayHasKey('Normal', $data['by_priority']);
        $this->assertArrayHasKey('High', $data['by_priority']);
        $this->assertArrayHasKey('Urgent', $data['by_priority']);
        $this->assertArrayHasKey('Critical', $data['by_priority']);
    }

    /** @test */
    public function issues_stats_api_returns_block_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create blocks with issues
        $block1 = Block::factory()->create(['name' => 'Block A']);
        $block2 = Block::factory()->create(['name' => 'Block B']);
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        // Create issues for blocks
        BlockIssue::factory()->count(3)->create(['block_id' => $block1->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        BlockIssue::factory()->count(2)->create(['block_id' => $block2->id, 'priority_id' => $priority->id, 'issue_status_id' => $issueStatus->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertArrayHasKey('by_block', $data);
        $this->assertIsArray($data['by_block']);
        
        // Should have blocks with issues
        $this->assertGreaterThan(0, count($data['by_block']));
        
        // Check structure of block data
        if (count($data['by_block']) > 0) {
            $blockData = $data['by_block'][0];
            $this->assertArrayHasKey('name', $blockData);
            $this->assertArrayHasKey('count', $blockData);
        }
    }

    /** @test */
    public function recent_issues_api_limits_results_to_five()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create 10 issues
        $block = Block::factory()->create();
        $priority = Priority::factory()->create();
        $issueStatus = IssueStatus::factory()->create();
        
        BlockIssue::factory()->count(10)->create([
            'block_id' => $block->id,
            'priority_id' => $priority->id,
            'issue_status_id' => $issueStatus->id
        ]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-issues');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // Should only return 5 most recent issues
        $this->assertCount(5, $data);
    }

    /** @test */
    public function recent_blocks_api_limits_results_to_five()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create 10 blocks
        $blockType = BlockType::factory()->create();
        Block::factory()->count(10)->create(['block_type_id' => $blockType->id]);
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-blocks');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // Should only return 5 most recent blocks
        $this->assertCount(5, $data);
    }
}
