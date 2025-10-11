<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlockInspection;
use App\Models\Block;
use App\Models\User;
use App\Models\BlockInspectionTeam;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BlockInspectionModelTest extends TestCase
{
    use DatabaseTransactions;

    public function test_block_inspection_has_correct_fillable_attributes()
    {
        $expectedFillable = [
            'block_id',
            'ref_no',
            'scheduled_date_time',
            'start_date_time',
            'end_date_time',
            'notes',
            'pdf_path',
            'pdf_name',
            'job_status_id',
            'is_mobile',
            'created_by',
            'updated_by',
            'deleted_by',
        ];

        $inspection = new BlockInspection();
        $this->assertEquals($expectedFillable, $inspection->getFillable());
    }

    public function test_block_inspection_has_correct_casts()
    {
        $expectedCasts = [
            'id' => 'int',
            'scheduled_date_time' => 'datetime',
            'start_date_time' => 'datetime',
            'end_date_time' => 'datetime',
            'is_mobile' => 'boolean',
            'job_status_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
            'deleted_at' => 'datetime',
        ];

        $inspection = new BlockInspection();
        $this->assertEquals($expectedCasts, $inspection->getCasts());
    }

    public function test_block_inspection_belongs_to_block()
    {
        $block = Block::factory()->create();
        $inspection = BlockInspection::factory()->create(['block_id' => $block->id]);

        $this->assertInstanceOf(Block::class, $inspection->block);
        $this->assertEquals($block->id, $inspection->block->id);
    }

    public function test_block_inspection_belongs_to_creator()
    {
        $user = User::factory()->create();
        $inspection = BlockInspection::factory()->create(['created_by' => $user->id]);

        $this->assertInstanceOf(User::class, $inspection->creator);
        $this->assertEquals($user->id, $inspection->creator->id);
    }

    public function test_block_inspection_has_many_inspection_teams()
    {
        $inspection = BlockInspection::factory()->create();
        $team1 = BlockInspectionTeam::factory()->create(['block_inspection_id' => $inspection->id]);
        $team2 = BlockInspectionTeam::factory()->create(['block_inspection_id' => $inspection->id]);

        $this->assertCount(2, $inspection->inspectionTeams);
        $this->assertInstanceOf(BlockInspectionTeam::class, $inspection->inspectionTeams->first());
    }

    public function test_status_text_attribute_returns_correct_text()
    {
        $inspection = BlockInspection::factory()->create(['job_status_id' => 1]);
        $this->assertEquals('Scheduled', $inspection->status_text);

        $inspection->update(['job_status_id' => 2]);
        $this->assertEquals('In Progress', $inspection->status_text);

        $inspection->update(['job_status_id' => 3]);
        $this->assertEquals('Completed', $inspection->status_text);

        $inspection->update(['job_status_id' => 4]);
        $this->assertEquals('Cancelled', $inspection->status_text);

        $inspection->update(['job_status_id' => 5]);
        $this->assertEquals('On Hold', $inspection->status_text);
    }

    public function test_status_color_attribute_returns_correct_color()
    {
        $inspection = BlockInspection::factory()->create(['job_status_id' => 1]);
        $this->assertEquals('info', $inspection->status_color);

        $inspection->update(['job_status_id' => 2]);
        $this->assertEquals('warning', $inspection->status_color);

        $inspection->update(['job_status_id' => 3]);
        $this->assertEquals('success', $inspection->status_color);

        $inspection->update(['job_status_id' => 4]);
        $this->assertEquals('danger', $inspection->status_color);

        $inspection->update(['job_status_id' => 5]);
        $this->assertEquals('secondary', $inspection->status_color);
    }

    public function test_active_scope_returns_correct_inspections()
    {
        // Create inspections with different statuses
        $scheduled = BlockInspection::factory()->scheduled()->create();
        $inProgress = BlockInspection::factory()->inProgress()->create();
        $completed = BlockInspection::factory()->completed()->create();
        $cancelled = BlockInspection::factory()->cancelled()->create();
        $onHold = BlockInspection::factory()->onHold()->create();

        $activeInspections = BlockInspection::active()->get();

        $this->assertCount(3, $activeInspections);
        $this->assertTrue($activeInspections->contains($scheduled));
        $this->assertTrue($activeInspections->contains($inProgress));
        $this->assertTrue($activeInspections->contains($onHold));
        $this->assertFalse($activeInspections->contains($completed));
        $this->assertFalse($activeInspections->contains($cancelled));
    }

    public function test_completed_scope_returns_correct_inspections()
    {
        $scheduled = BlockInspection::factory()->scheduled()->create();
        $completed = BlockInspection::factory()->completed()->create();

        $completedInspections = BlockInspection::completed()->get();

        $this->assertCount(1, $completedInspections);
        $this->assertTrue($completedInspections->contains($completed));
        $this->assertFalse($completedInspections->contains($scheduled));
    }

    public function test_generate_ref_no_creates_unique_reference_numbers()
    {
        $ref1 = BlockInspection::generateRefNo();
        $ref2 = BlockInspection::generateRefNo();

        $this->assertStringStartsWith('INSP', $ref1);
        $this->assertStringStartsWith('INSP', $ref2);
        $this->assertEquals(14, strlen($ref1)); // INSP + YYYY + MM + 0001
        $this->assertEquals(14, strlen($ref2));
        
        // Test that the format is correct
        $this->assertMatchesRegularExpression('/^INSP\d{6}\d{4}$/', $ref1);
        $this->assertMatchesRegularExpression('/^INSP\d{6}\d{4}$/', $ref2);
    }

    public function test_generate_ref_no_increments_sequence()
    {
        // Create an inspection with a specific ref_no to test sequence increment
        $inspection = BlockInspection::factory()->create([
            'ref_no' => 'INSP2025100001'
        ]);
        
        $newRef = BlockInspection::generateRefNo();
        
        $this->assertStringStartsWith('INSP202510', $newRef);
        $this->assertEquals('INSP2025100002', $newRef);
    }
}
