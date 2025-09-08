<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\UserType;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTypeTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function it_can_scope_visible_user_types()
    {
        // Create visible user type
        $visibleType = UserType::create([
            'name' => 'Admin',
            'description' => 'Administrator',
            'is_hidden' => false,
        ]);

        // Create hidden user type
        $hiddenType = UserType::create([
            'name' => 'Financial Admin',
            'description' => 'Financial Administrator',
            'is_hidden' => true,
        ]);

        // Test visible scope
        $visibleTypes = UserType::visible()->get();
        $this->assertCount(1, $visibleTypes);
        $this->assertEquals($visibleType->id, $visibleTypes->first()->id);
        $this->assertFalse($visibleTypes->contains($hiddenType));
    }

    /** @test */
    public function it_has_users_relationship()
    {
        // Create user type
        $userType = UserType::create([
            'name' => 'Contractor Admin',
            'description' => 'Contractor Administrator',
            'is_hidden' => false,
        ]);

        // Create users with this type
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
        ]);

        // Test users relationship
        $this->assertCount(2, $userType->users);
        $this->assertTrue($userType->users->contains($user1));
        $this->assertTrue($userType->users->contains($user2));
    }

    /** @test */
    public function it_casts_is_hidden_to_boolean()
    {
        $userType = UserType::create([
            'name' => 'Test Type',
            'description' => 'Test Description',
            'is_hidden' => 1, // Should be cast to boolean true
        ]);

        $this->assertTrue($userType->is_hidden);
        $this->assertIsBool($userType->is_hidden);
    }

    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $userType = new UserType();
        $expectedFillable = ['name', 'description', 'is_hidden'];
        
        $this->assertEquals($expectedFillable, $userType->getFillable());
    }
}
