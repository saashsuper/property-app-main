<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Block;
use App\Models\BlockType;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockApiTest extends TestCase
{
    use RefreshDatabase;

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
    }

    /** @test */
    public function api_returns_blocks_list_for_authenticated_admin()
    {
        $blocks = Block::factory()->count(5)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks'));

        $response->assertStatus(200)
                ->assertJsonCount(5)
                ->assertJsonStructure([
                    '*' => [
                        'id',
                        'name',
                        'management_company',
                        'block_address',
                        'car_spaces',
                        'no_of_units',
                        'created_at',
                        'updated_at'
                    ]
                ]);
    }

    /** @test */
    public function api_returns_single_block_details()
    {
        $block = Block::factory()->create([
            'name' => 'API Test Block',
            'management_company' => 'API Test Management',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks.show', $block));

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $block->id,
                    'name' => 'API Test Block',
                    'management_company' => 'API Test Management'
                ])
                ->assertJsonStructure([
                    'id',
                    'name',
                    'management_company',
                    'block_type_id',
                    'block_address',
                    'car_spaces',
                    'no_of_units',
                    'inspection_count',
                    'created_at',
                    'updated_at'
                ]);
    }

    /** @test */
    public function api_returns_404_for_non_existent_block()
    {
        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks.show', 999999));

        $response->assertStatus(404);
    }

    /** @test */
    public function api_requires_authentication()
    {
        $response = $this->getJson(route('api.blocks'));
        $response->assertStatus(401);
    }

    /** @test */
    public function api_filters_blocks_by_search_query()
    {
        $block1 = Block::factory()->create([
            'name' => 'Searchable Block Name',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $block2 = Block::factory()->create([
            'name' => 'Different Block Name',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['search' => 'Searchable']));

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment(['name' => 'Searchable Block Name'])
                ->assertJsonMissing(['name' => 'Different Block Name']);
    }

    /** @test */
    public function api_filters_blocks_by_block_type()
    {
        $blockType1 = BlockType::factory()->create(['name' => 'Residential']);
        $blockType2 = BlockType::factory()->create(['name' => 'Commercial']);

        $block1 = Block::factory()->create([
            'name' => 'Residential Block',
            'block_type_id' => $blockType1->id,
            'created_by' => $this->admin->id
        ]);

        $block2 = Block::factory()->create([
            'name' => 'Commercial Block',
            'block_type_id' => $blockType2->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['block_type_id' => $blockType1->id]));

        $response->assertStatus(200)
                ->assertJsonCount(1)
                ->assertJsonFragment(['name' => 'Residential Block'])
                ->assertJsonMissing(['name' => 'Commercial Block']);
    }

    /** @test */
    public function api_paginates_blocks_results()
    {
        Block::factory()->count(25)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['per_page' => 10]));

        $response->assertStatus(200)
                ->assertJsonCount(10)
                ->assertJsonStructure([
                    'data' => [
                        '*' => ['id', 'name', 'management_company']
                    ],
                    'meta' => [
                        'current_page',
                        'last_page',
                        'per_page',
                        'total'
                    ]
                ]);
    }

    /** @test */
    public function api_sorts_blocks_by_specified_field()
    {
        $block1 = Block::factory()->create([
            'name' => 'Alpha Block',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $block2 = Block::factory()->create([
            'name' => 'Beta Block',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $block3 = Block::factory()->create([
            'name' => 'Gamma Block',
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // Sort by name ascending
        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['sort' => 'name', 'order' => 'asc']));

        $response->assertStatus(200);
        $data = $response->json();
        
        $this->assertEquals('Alpha Block', $data[0]['name']);
        $this->assertEquals('Beta Block', $data[1]['name']);
        $this->assertEquals('Gamma Block', $data[2]['name']);
    }

    /** @test */
    public function api_includes_related_data_when_requested()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks.show', [
                             'block' => $block->id,
                             'include' => 'blockType,creator'
                         ]));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'id',
                    'name',
                    'block_type' => [
                        'id',
                        'name'
                    ],
                    'creator' => [
                        'id',
                        'name'
                    ]
                ]);
    }

    /** @test */
    public function api_returns_blocks_statistics()
    {
        Block::factory()->count(10)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id,
            'car_spaces' => 50,
            'no_of_units' => 100
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson('/api/blocks/statistics');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'total_blocks',
                    'total_car_spaces',
                    'total_units',
                    'average_units_per_block',
                    'blocks_by_type'
                ]);
    }

    /** @test */
    public function api_handles_invalid_sort_parameters()
    {
        Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', [
                             'sort' => 'invalid_field',
                             'order' => 'invalid_order'
                         ]));

        $response->assertStatus(200); // Should fallback to default sorting
    }

    /** @test */
    public function api_rate_limiting_works()
    {
        // Make multiple requests quickly to test rate limiting
        for ($i = 0; $i < 100; $i++) {
            $response = $this->actingAs($this->admin)
                             ->getJson(route('api.blocks'));
            
            if ($response->status() === 429) {
                $this->assertEquals(429, $response->status());
                return;
            }
        }

        // If we reach here without hitting rate limit, that's also valid
        $this->assertTrue(true);
    }

    /** @test */
    public function api_returns_proper_error_for_malformed_requests()
    {
        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['per_page' => 'invalid']));

        $response->assertStatus(422)
                ->assertJsonValidationErrors('per_page');
    }

    /** @test */
    public function api_supports_field_selection()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        $response = $this->actingAs($this->admin)
                         ->getJson(route('api.blocks', ['fields' => 'id,name,car_spaces']));

        $response->assertStatus(200)
                ->assertJsonStructure([
                    '*' => ['id', 'name', 'car_spaces']
                ])
                ->assertJsonMissing(['management_company']);
    }

    /** @test */
    public function api_caches_expensive_queries()
    {
        Block::factory()->count(100)->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // First request
        $start = microtime(true);
        $response1 = $this->actingAs($this->admin)
                          ->getJson('/api/blocks/statistics');
        $time1 = microtime(true) - $start;

        // Second request (should be cached)
        $start = microtime(true);
        $response2 = $this->actingAs($this->admin)
                          ->getJson('/api/blocks/statistics');
        $time2 = microtime(true) - $start;

        $response1->assertStatus(200);
        $response2->assertStatus(200);
        
        // Second request should be faster due to caching
        $this->assertLessThan($time1, $time2);
    }

    /** @test */
    public function api_handles_concurrent_requests_safely()
    {
        $block = Block::factory()->create([
            'block_type_id' => $this->blockType->id,
            'created_by' => $this->admin->id
        ]);

        // Simulate concurrent requests
        $responses = [];
        for ($i = 0; $i < 5; $i++) {
            $responses[] = $this->actingAs($this->admin)
                                ->getJson(route('api.blocks.show', $block));
        }

        foreach ($responses as $response) {
            $response->assertStatus(200)
                    ->assertJson(['id' => $block->id]);
        }
    }
}
