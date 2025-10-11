<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\BlockInspection;
use App\Models\Block;
use App\Models\User;
use App\Models\BlockInspectionTeam;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BlockInspectionApiOnlyTest extends TestCase
{
    use DatabaseTransactions;

    protected $admin;
    protected $block;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user
        $this->admin = User::factory()->admin()->create();
        $this->block = Block::factory()->create();
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

    public function test_inspection_team_model_relationships()
    {
        $inspection = BlockInspection::factory()->create();
        $user = User::factory()->create();
        $team = BlockInspectionTeam::factory()->create([
            'block_inspection_id' => $inspection->id,
            'user_id' => $user->id
        ]);

        // Test inspection relationship
        $this->assertInstanceOf(BlockInspection::class, $team->blockInspection);
        $this->assertEquals($inspection->id, $team->blockInspection->id);

        // Test user relationship
        $this->assertInstanceOf(User::class, $team->user);
        $this->assertEquals($user->id, $team->user->id);
    }

    public function test_inspection_team_factory_states()
    {
        $inspection = BlockInspection::factory()->create();
        
        $leadTeam = BlockInspectionTeam::factory()->lead()->create(['block_inspection_id' => $inspection->id]);
        $this->assertEquals('Lead Inspector', $leadTeam->role);
        $this->assertTrue($leadTeam->is_lead);

        $inspectorTeam = BlockInspectionTeam::factory()->inspector()->create(['block_inspection_id' => $inspection->id]);
        $this->assertEquals('Inspector', $inspectorTeam->role);
        $this->assertFalse($inspectorTeam->is_lead);

        $assistantTeam = BlockInspectionTeam::factory()->assistant()->create(['block_inspection_id' => $inspection->id]);
        $this->assertEquals('Assistant Inspector', $assistantTeam->role);
        $this->assertFalse($assistantTeam->is_lead);
    }

    public function test_inspection_factory_states()
    {
        $scheduled = BlockInspection::factory()->scheduled()->create();
        $this->assertEquals(1, $scheduled->job_status_id);
        $this->assertNull($scheduled->start_date_time);
        $this->assertNull($scheduled->end_date_time);

        $inProgress = BlockInspection::factory()->inProgress()->create();
        $this->assertEquals(2, $inProgress->job_status_id);
        $this->assertNotNull($inProgress->start_date_time);
        $this->assertNull($inProgress->end_date_time);

        $completed = BlockInspection::factory()->completed()->create();
        $this->assertEquals(3, $completed->job_status_id);
        $this->assertNotNull($completed->start_date_time);
        $this->assertNotNull($completed->end_date_time);
    }
}
