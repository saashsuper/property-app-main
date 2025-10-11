<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DashboardApiOnlyTest extends TestCase
{
    use DatabaseTransactions;

    protected $adminType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types
        $this->adminType = UserType::factory()->admin()->create();
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
    public function dashboard_stats_api_returns_json_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
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
    }

    /** @test */
    public function issues_stats_api_returns_json_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'by_status',
            'by_priority',
            'by_block',
            'trend'
        ]);
    }

    /** @test */
    public function recent_issues_api_returns_json_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
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
    }

    /** @test */
    public function recent_blocks_api_returns_json_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
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
    public function dashboard_stats_api_returns_numeric_values()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        // All stats should be numeric
        $this->assertIsNumeric($data['total_blocks']);
        $this->assertIsNumeric($data['total_block_types']);
        $this->assertIsNumeric($data['total_units']);
        $this->assertIsNumeric($data['total_issues']);
        $this->assertIsNumeric($data['open_issues']);
        $this->assertIsNumeric($data['in_progress_issues']);
        $this->assertIsNumeric($data['resolved_issues']);
        $this->assertIsNumeric($data['high_priority_issues']);
    }

    /** @test */
    public function dashboard_api_endpoints_return_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $endpoints = [
            '/api/dashboard/issues-stats',
            '/api/dashboard/stats',
            '/api/dashboard/recent-issues',
            '/api/dashboard/recent-blocks'
        ];
        
        foreach ($endpoints as $endpoint) {
            $response = $this->get($endpoint);
            $response->assertStatus(200);
            $response->assertHeader('content-type', 'application/json');
        }
    }

    /** @test */
    public function issues_stats_api_returns_priority_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
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
    public function issues_stats_api_returns_status_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/issues-stats');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertArrayHasKey('by_status', $data);
        $this->assertArrayHasKey('Open', $data['by_status']);
        $this->assertArrayHasKey('In Progress', $data['by_status']);
        $this->assertArrayHasKey('Resolved', $data['by_status']);
        $this->assertArrayHasKey('Closed', $data['by_status']);
        $this->assertArrayHasKey('On Hold', $data['by_status']);
    }

    /** @test */
    public function recent_issues_api_returns_array()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-issues');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertIsArray($data);
    }

    /** @test */
    public function recent_blocks_api_returns_array()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/dashboard/recent-blocks');
        
        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertIsArray($data);
    }
}
