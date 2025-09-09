<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\BlockInspectionTeam;
use App\Models\BlockInspection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BlockInspectionTeamModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_block_inspection_team_has_correct_fillable_attributes()
    {
        $expectedFillable = [
            'block_inspection_id',
            'user_id',
            'role',
            'is_lead',
        ];

        $team = new BlockInspectionTeam();
        $this->assertEquals($expectedFillable, $team->getFillable());
    }

    public function test_block_inspection_team_has_correct_casts()
    {
        $expectedCasts = [
            'id' => 'int',
            'block_inspection_id' => 'integer',
            'user_id' => 'integer',
            'is_lead' => 'boolean',
            'deleted_at' => 'datetime',
        ];

        $team = new BlockInspectionTeam();
        $this->assertEquals($expectedCasts, $team->getCasts());
    }

    public function test_block_inspection_team_belongs_to_block_inspection()
    {
        $inspection = BlockInspection::factory()->create();
        $team = BlockInspectionTeam::factory()->create(['block_inspection_id' => $inspection->id]);

        $this->assertInstanceOf(BlockInspection::class, $team->blockInspection);
        $this->assertEquals($inspection->id, $team->blockInspection->id);
    }

    public function test_block_inspection_team_belongs_to_user()
    {
        $user = User::factory()->create();
        $team = BlockInspectionTeam::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $team->user);
        $this->assertEquals($user->id, $team->user->id);
    }

    public function test_lead_inspector_has_correct_role_and_flag()
    {
        $team = BlockInspectionTeam::factory()->lead()->create();

        $this->assertEquals('Lead Inspector', $team->role);
        $this->assertTrue($team->is_lead);
    }

    public function test_regular_inspector_has_correct_role_and_flag()
    {
        $team = BlockInspectionTeam::factory()->inspector()->create();

        $this->assertEquals('Inspector', $team->role);
        $this->assertFalse($team->is_lead);
    }

    public function test_assistant_inspector_has_correct_role_and_flag()
    {
        $team = BlockInspectionTeam::factory()->assistant()->create();

        $this->assertEquals('Assistant Inspector', $team->role);
        $this->assertFalse($team->is_lead);
    }
}
