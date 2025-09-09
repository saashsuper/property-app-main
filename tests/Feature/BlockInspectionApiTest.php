<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BlockInspection;
use App\Models\Block;
use App\Models\User;
use App\Models\BlockInspectionTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class BlockInspectionApiTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $block;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->admin()->create();
        $this->block = Block::factory()->create();
    }

    public function test_index_returns_inspections_data()
    {
        // Create some inspections
        $inspection1 = BlockInspection::factory()->create(['block_id' => $this->block->id]);
        $inspection2 = BlockInspection::factory()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.index'));

        $response->assertStatus(200);
        $response->assertViewIs('block-inspections.index');
        $response->assertViewHas('inspections');
        
        $inspections = $response->viewData('inspections');
        $this->assertTrue($inspections->contains($inspection1));
        $this->assertTrue($inspections->contains($inspection2));
    }

    public function test_index_filters_by_status()
    {
        $scheduledInspection = BlockInspection::factory()->scheduled()->create(['block_id' => $this->block->id]);
        $completedInspection = BlockInspection::factory()->completed()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.index', ['status' => 1]));

        $response->assertStatus(200);
        $response->assertViewHas('inspections');
        
        $inspections = $response->viewData('inspections');
        $this->assertTrue($inspections->contains($scheduledInspection));
        $this->assertFalse($inspections->contains($completedInspection));
    }

    public function test_index_filters_by_date_range()
    {
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        $inspection1 = BlockInspection::factory()->create([
            'block_id' => $this->block->id,
            'scheduled_date_time' => now()->addDay()
        ]);
        $inspection2 = BlockInspection::factory()->create([
            'block_id' => $this->block->id,
            'scheduled_date_time' => now()->addDays(5)
        ]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.index', [
                'date_from' => $today,
                'date_to' => $tomorrow
            ]));

        $response->assertStatus(200);
        $inspections = $response->viewData('inspections');
        $this->assertTrue($inspections->contains($inspection1));
        $this->assertFalse($inspections->contains($inspection2));
    }

    public function test_index_searches_by_reference_number()
    {
        $inspection1 = BlockInspection::factory()->create([
            'block_id' => $this->block->id,
            'ref_no' => 'INSP2024010001'
        ]);
        $inspection2 = BlockInspection::factory()->create([
            'block_id' => $this->block->id,
            'ref_no' => 'INSP2024010002'
        ]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.index', [
                'search' => 'INSP2024010001'
            ]));

        $response->assertStatus(200);
        $inspections = $response->viewData('inspections');
        $this->assertTrue($inspections->contains($inspection1));
        $this->assertFalse($inspections->contains($inspection2));
    }

    public function test_index_searches_by_block_name()
    {
        $block1 = Block::factory()->create(['name' => 'Block A']);
        $block2 = Block::factory()->create(['name' => 'Block B']);
        
        $inspection1 = BlockInspection::factory()->create(['block_id' => $block1->id]);
        $inspection2 = BlockInspection::factory()->create(['block_id' => $block2->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.index', [
                'search' => 'Block A'
            ]));

        $response->assertStatus(200);
        $inspections = $response->viewData('inspections');
        $this->assertTrue($inspections->contains($inspection1));
        $this->assertFalse($inspections->contains($inspection2));
    }

    public function test_store_creates_new_inspection()
    {
        $user = User::factory()->create();
        $teamMembers = User::factory()->count(2)->create();
        
        $data = [
            'block_id' => $this->block->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Test inspection notes',
            'team_members' => $teamMembers->pluck('id')->toArray(),
            'lead_inspector' => $user->id,
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.store'), $data);

        $response->assertRedirect(route('block-inspections.index'));
        $response->assertSessionHas('success', 'Inspection scheduled successfully.');
        
        $this->assertDatabaseHas('block_inspections', [
            'block_id' => $this->block->id,
            'notes' => 'Test inspection notes',
            'created_by' => $this->admin->id,
        ]);

        // Check that team members were created
        $inspection = BlockInspection::where('block_id', $this->block->id)->first();
        $this->assertCount(3, $inspection->inspectionTeams); // 2 team members + 1 lead inspector
        
        // Check that lead inspector is marked correctly
        $leadInspector = $inspection->inspectionTeams->where('is_lead', true)->first();
        $this->assertEquals($user->id, $leadInspector->user_id);
    }

    public function test_store_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.store'), []);

        $response->assertSessionHasErrors(['block_id', 'scheduled_date_time', 'team_members', 'lead_inspector']);
    }

    public function test_store_validates_scheduled_date_is_future()
    {
        $user = User::factory()->create();
        
        $data = [
            'block_id' => $this->block->id,
            'scheduled_date_time' => now()->subDay()->format('Y-m-d H:i:s'), // Past date
            'notes' => 'Test inspection notes',
            'team_members' => [$user->id],
            'lead_inspector' => $user->id,
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.store'), $data);

        $response->assertSessionHasErrors(['scheduled_date_time']);
    }

    public function test_show_returns_json_for_ajax_requests()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->get(route('block-inspections.show', $inspection));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $inspection->id,
                'block_id' => $inspection->block_id,
            ]
        ]);
    }

    public function test_update_modifies_inspection()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);
        $team = BlockInspectionTeam::factory()->lead()->create(['block_inspection_id' => $inspection->id]);
        $newUser = User::factory()->create();

        $data = [
            'user_id' => $newUser->id,
            'scheduled_date_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated inspection notes',
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->put(route('block-inspections.update', $inspection), $data);

        $response->assertRedirect(route('block-inspections.index'));
        $response->assertSessionHas('success', 'Inspection updated successfully.');
        
        $inspection->refresh();
        $this->assertEquals('Updated inspection notes', $inspection->notes);
        $this->assertEquals($this->admin->id, $inspection->updated_by);
        
        // Check that team was updated
        $this->assertCount(1, $inspection->inspectionTeams);
        $this->assertEquals($newUser->id, $inspection->inspectionTeams->first()->user_id);
    }

    public function test_update_returns_json_for_ajax_requests()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);
        $team = BlockInspectionTeam::factory()->lead()->create(['block_inspection_id' => $inspection->id]);
        $newUser = User::factory()->create();

        $data = [
            'user_id' => $newUser->id,
            'scheduled_date_time' => now()->addDays(2)->format('Y-m-d H:i:s'),
            'notes' => 'Updated inspection notes',
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->put(route('block-inspections.update', $inspection), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Inspection updated successfully!'
        ]);
    }

    public function test_destroy_deletes_inspection()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('block-inspections.destroy', $inspection));

        $response->assertRedirect(route('block-inspections.index'));
        $response->assertSessionHas('success', 'Inspection deleted successfully.');
        
        $this->assertSoftDeleted('block_inspections', ['id' => $inspection->id]);
        $this->assertEquals($this->admin->id, $inspection->fresh()->deleted_by);
    }

    public function test_destroy_returns_json_for_ajax_requests()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->delete(route('block-inspections.destroy', $inspection));

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Inspection deleted successfully!'
        ]);
    }

    public function test_start_updates_inspection_status()
    {
        $inspection = BlockInspection::factory()->scheduled()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.start', $inspection));

        $response->assertRedirect(route('block-inspections.show', $inspection));
        $response->assertSessionHas('success', 'Inspection started successfully.');
        
        $inspection->refresh();
        $this->assertEquals(2, $inspection->job_status_id); // In Progress
        $this->assertNotNull($inspection->start_date_time);
        $this->assertEquals($this->admin->id, $inspection->updated_by);
    }

    public function test_complete_updates_inspection_status()
    {
        $inspection = BlockInspection::factory()->inProgress()->create(['block_id' => $this->block->id]);

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.complete', $inspection));

        $response->assertRedirect(route('block-inspections.show', $inspection));
        $response->assertSessionHas('success', 'Inspection completed successfully.');
        
        $inspection->refresh();
        $this->assertEquals(3, $inspection->job_status_id); // Completed
        $this->assertNotNull($inspection->end_date_time);
        $this->assertEquals($this->admin->id, $inspection->updated_by);
    }

    public function test_store_from_modal_creates_inspection_via_ajax()
    {
        $user = User::factory()->create();
        
        $data = [
            'block_id' => $this->block->id,
            'user_id' => $user->id,
            'scheduled_date_time' => now()->addDay()->format('Y-m-d H:i:s'),
            'notes' => 'Modal inspection notes',
        ];

        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.store-from-modal'), $data);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Inspection scheduled successfully!'
        ]);
        
        $this->assertDatabaseHas('block_inspections', [
            'block_id' => $this->block->id,
            'notes' => 'Modal inspection notes',
            'created_by' => $this->admin->id,
        ]);
    }

    public function test_store_from_modal_validates_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->withHeaders(['Accept' => 'application/json'])
            ->post(route('block-inspections.store-from-modal'), []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['block_id', 'user_id', 'scheduled_date_time', 'notes']);
    }

    public function test_inspection_model_relationships_work_correctly()
    {
        $inspection = BlockInspection::factory()->create(['block_id' => $this->block->id]);
        $team = BlockInspectionTeam::factory()->lead()->create(['block_inspection_id' => $inspection->id]);

        // Test block relationship
        $this->assertInstanceOf(Block::class, $inspection->block);
        $this->assertEquals($this->block->id, $inspection->block->id);

        // Test creator relationship
        $this->assertInstanceOf(User::class, $inspection->creator);
        $this->assertEquals($inspection->created_by, $inspection->creator->id);

        // Test team relationship
        $this->assertCount(1, $inspection->inspectionTeams);
        $this->assertInstanceOf(BlockInspectionTeam::class, $inspection->inspectionTeams->first());
    }

    public function test_inspection_status_attributes_work_correctly()
    {
        $inspection = BlockInspection::factory()->scheduled()->create();
        
        $this->assertEquals('Scheduled', $inspection->status_text);
        $this->assertEquals('info', $inspection->status_color);

        $inspection->update(['job_status_id' => 2]);
        $this->assertEquals('In Progress', $inspection->status_text);
        $this->assertEquals('warning', $inspection->status_color);

        $inspection->update(['job_status_id' => 3]);
        $this->assertEquals('Completed', $inspection->status_text);
        $this->assertEquals('success', $inspection->status_color);
    }

    public function test_inspection_scopes_work_correctly()
    {
        $scheduled = BlockInspection::factory()->scheduled()->create();
        $inProgress = BlockInspection::factory()->inProgress()->create();
        $completed = BlockInspection::factory()->completed()->create();

        $activeInspections = BlockInspection::active()->get();
        $this->assertCount(2, $activeInspections);
        $this->assertTrue($activeInspections->contains($scheduled));
        $this->assertTrue($activeInspections->contains($inProgress));
        $this->assertFalse($activeInspections->contains($completed));

        $completedInspections = BlockInspection::completed()->get();
        $this->assertCount(1, $completedInspections);
        $this->assertTrue($completedInspections->contains($completed));
    }
}
