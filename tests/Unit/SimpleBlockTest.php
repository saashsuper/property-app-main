<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Block;

class SimpleBlockTest extends TestCase
{
    /** @test */
    public function block_model_exists()
    {
        $this->assertTrue(class_exists(Block::class));
    }

    /** @test */
    public function block_has_correct_fillable_attributes()
    {
        $block = new Block();
        
        $expectedFillable = [
            'name',
            'management_company',
            'block_type_id',
            'user_id',
            'block_manager_id',
            'address1',
            'address2',
            'address3',
            'block_address',
            'management_company_address',
            'country_id',
            'state_id',
            'car_spaces',
            'inspection_count',
            'no_of_units',
            'created_by',
            'updated_by',
            'deleted_by',
        ];

        $this->assertEquals($expectedFillable, $block->getFillable());
    }

    /** @test */
    public function block_has_correct_casts()
    {
        $block = new Block();
        
        $expectedCasts = [
            'id' => 'int',
            'car_spaces' => 'integer',
            'inspection_count' => 'integer',
            'no_of_units' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
            'deleted_at' => 'datetime',
        ];

        $casts = $block->getCasts();
        
        foreach ($expectedCasts as $attribute => $expectedType) {
            $this->assertArrayHasKey($attribute, $casts);
            $this->assertEquals($expectedType, $casts[$attribute]);
        }
    }

    /** @test */
    public function block_uses_soft_deletes()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(Block::class)));
    }

    /** @test */
    public function block_uses_has_factory()
    {
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses(Block::class)));
    }
}
