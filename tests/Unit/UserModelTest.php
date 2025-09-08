<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;

class UserModelTest extends TestCase
{
    /** @test */
    public function it_has_correct_fillable_attributes()
    {
        $user = new User();
        $expectedFillable = ['name', 'email', 'password', 'avatar', 'user_type_id', 'created_by'];
        
        $this->assertEquals($expectedFillable, $user->getFillable());
    }

    /** @test */
    public function it_has_correct_hidden_attributes()
    {
        $user = new User();
        $expectedHidden = ['password', 'remember_token'];
        
        $this->assertEquals($expectedHidden, $user->getHidden());
    }

    /** @test */
    public function it_has_correct_casts()
    {
        $user = new User();
        $expectedCasts = ['email_verified_at' => 'datetime', 'id' => 'int'];
        
        $this->assertEquals($expectedCasts, $user->getCasts());
    }

    /** @test */
    public function it_uses_correct_traits()
    {
        $user = new User();
        
        $this->assertTrue(in_array('Laravel\Sanctum\HasApiTokens', class_uses($user)));
        $this->assertTrue(in_array('Illuminate\Database\Eloquent\Factories\HasFactory', class_uses($user)));
        $this->assertTrue(in_array('Illuminate\Notifications\Notifiable', class_uses($user)));
    }

    /** @test */
    public function it_implements_correct_interface()
    {
        $user = new User();
        
        $this->assertInstanceOf('Illuminate\Contracts\Auth\MustVerifyEmail', $user);
    }

    /** @test */
    public function it_has_user_type_relationship_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'userType'));
    }

    /** @test */
    public function it_has_creator_relationship_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'creator'));
    }

    /** @test */
    public function it_has_created_users_relationship_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'createdUsers'));
    }

    /** @test */
    public function it_has_is_admin_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'isAdmin'));
    }

    /** @test */
    public function it_has_has_type_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'hasType'));
    }

    /** @test */
    public function it_has_active_scope_method()
    {
        $user = new User();
        
        $this->assertTrue(method_exists($user, 'scopeActive'));
    }
}
