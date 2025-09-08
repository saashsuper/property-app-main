<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\UserType;

class UserTypeModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $userType = new UserType();
        $expectedFillable = ['name', 'description', 'is_hidden'];
        
        $this->assertEquals($expectedFillable, $userType->getFillable());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $userType = new UserType();
        $expectedCasts = ['is_hidden' => 'boolean', 'id' => 'int', 'deleted_at' => 'datetime'];
        
        $this->assertEquals($expectedCasts, $userType->getCasts());
    }

    /** @test */
    public function it_uses_correct_traits()
    {
        $userType = new UserType();
        
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($userType)));
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($userType)));
    }

    /** @test */
    public function it_has_correct_table_name()
    {
        $userType = new UserType();
        
        $this->assertEquals('user_types', $userType->getTable());
    }

    /** @test */
    public function it_has_visible_scope_method()
    {
        $userType = new UserType();
        
        $this->assertTrue(method_exists($userType, 'scopeVisible'));
    }

    /** @test */
    public function it_has_users_relationship_method()
    {
        $userType = new UserType();
        
        $this->assertTrue(method_exists($userType, 'users'));
    }
}
