<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTypeFactoryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function it_can_create_a_user_type_with_factory()
    {
        $userType = UserType::factory()->create();
        
        $this->assertInstanceOf(UserType::class, $userType);
        $this->assertNotNull($userType->name);
        $this->assertNotNull($userType->description);
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_a_hidden_user_type()
    {
        $userType = UserType::factory()->hidden()->create();
        
        $this->assertTrue($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_a_visible_user_type()
    {
        $userType = UserType::factory()->visible()->create();
        
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_a_contractor_admin_user_type()
    {
        $userType = UserType::factory()->contractorAdmin()->create();
        
        $this->assertEquals('Contractor Admin', $userType->name);
        $this->assertEquals('Contractor Administrator with management access over contractor users', $userType->description);
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_a_contractor_user_type()
    {
        $userType = UserType::factory()->contractorUser()->create();
        
        $this->assertEquals('Contractor User', $userType->name);
        $this->assertEquals('Contractor User with limited access for assigned tasks', $userType->description);
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_an_admin_user_type()
    {
        $userType = UserType::factory()->admin()->create();
        
        $this->assertEquals('Admin', $userType->name);
        $this->assertEquals('System Administrator with full access', $userType->description);
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_a_financial_admin_user_type()
    {
        $userType = UserType::factory()->financialAdmin()->create();
        
        $this->assertEquals('Financial Admin', $userType->name);
        $this->assertEquals('Financial Administrator with financial management access', $userType->description);
        $this->assertTrue($userType->is_hidden);
    }

    /** @test */
    public function it_can_create_multiple_user_types_with_factory()
    {
        $userTypes = UserType::factory()->count(3)->create();
        
        $this->assertCount(3, $userTypes);
        $this->assertInstanceOf(UserType::class, $userTypes->first());
    }

    /** @test */
    public function it_can_scope_visible_user_types()
    {
        UserType::factory()->visible()->count(2)->create();
        UserType::factory()->hidden()->count(1)->create();
        
        $visibleTypes = UserType::visible()->get();
        $this->assertCount(2, $visibleTypes);
        
        foreach ($visibleTypes as $type) {
            $this->assertFalse($type->is_hidden);
        }
    }
}
