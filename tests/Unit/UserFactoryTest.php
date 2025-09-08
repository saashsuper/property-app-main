<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserFactoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_create_a_user_with_factory()
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(User::class, $user);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
    }

    /** @test */
    public function it_can_create_a_user_with_specific_user_type()
    {
        $userType = UserType::factory()->contractorAdmin()->create();
        $user = User::factory()->withUserType($userType->id)->create();
        
        $this->assertEquals($userType->id, $user->user_type_id);
        $this->assertEquals('Contractor Admin', $user->userType->name);
    }

    /** @test */
    public function it_can_create_a_contractor_admin_user()
    {
        $userType = UserType::factory()->contractorAdmin()->create();
        $user = User::factory()->contractorAdmin()->create();
        
        $this->assertTrue($user->hasType('Contractor Admin'));
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_can_create_a_contractor_user()
    {
        $userType = UserType::factory()->contractorUser()->create();
        $user = User::factory()->contractorUser()->create();
        
        $this->assertTrue($user->hasType('Contractor User'));
        $this->assertFalse($user->isAdmin());
    }

    /** @test */
    public function it_can_create_a_user_created_by_another_user()
    {
        $creator = User::factory()->create();
        $user = User::factory()->createdBy($creator->id)->create();
        
        $this->assertEquals($creator->id, $user->created_by);
        $this->assertEquals($creator->id, $user->creator->id);
    }

    /** @test */
    public function it_can_create_multiple_users_with_factory()
    {
        $users = User::factory()->count(5)->create();
        
        $this->assertCount(5, $users);
        $this->assertInstanceOf(User::class, $users->first());
    }

    /** @test */
    public function it_can_create_users_with_different_states()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $contractorUser = User::factory()->contractorUser()->create();
        $unverifiedUser = User::factory()->unverified()->create();
        
        $this->assertTrue($contractorAdmin->hasType('Contractor Admin'));
        $this->assertTrue($contractorUser->hasType('Contractor User'));
        $this->assertNull($unverifiedUser->email_verified_at);
    }
}
