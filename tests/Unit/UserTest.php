<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;

class UserTest extends TestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Manually configure database for testing
        config(['database.connections.sqlite.database' => ':memory:']);
        config(['database.default' => 'sqlite']);
        
        // Run migrations
        $this->artisan('migrate');
    }

    /** @test */
    public function it_can_check_if_user_has_specific_type()
    {
        // Create a user type
        $userType = UserType::create([
            'name' => 'Contractor Admin',
            'description' => 'Contractor Administrator',
            'is_hidden' => false,
        ]);

        // Create a user with that type
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
        ]);

        // Test hasType method
        $this->assertTrue($user->hasType('Contractor Admin'));
        $this->assertFalse($user->hasType('Admin'));
        $this->assertFalse($user->hasType('Non-existent Type'));
    }

    /** @test */
    public function it_can_check_if_user_is_admin()
    {
        // Create admin user type
        $adminType = UserType::create([
            'name' => 'Admin',
            'description' => 'Administrator',
            'is_hidden' => false,
        ]);

        // Create contractor admin user type
        $contractorAdminType = UserType::create([
            'name' => 'Contractor Admin',
            'description' => 'Contractor Administrator',
            'is_hidden' => false,
        ]);

        // Create users
        $adminUser = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $adminType->id,
        ]);

        $contractorUser = User::create([
            'name' => 'Contractor User',
            'email' => 'contractor@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $contractorAdminType->id,
        ]);

        // Test isAdmin method
        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($contractorUser->isAdmin());
    }

    /** @test */
    public function it_has_creator_relationship()
    {
        // Create a creator user
        $creator = User::create([
            'name' => 'Creator',
            'email' => 'creator@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create a user type
        $userType = UserType::create([
            'name' => 'Contractor User',
            'description' => 'Contractor User',
            'is_hidden' => false,
        ]);

        // Create a user created by the creator
        $createdUser = User::create([
            'name' => 'Created User',
            'email' => 'created@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
            'created_by' => $creator->id,
        ]);

        // Test creator relationship
        $this->assertEquals($creator->id, $createdUser->creator->id);
        $this->assertEquals($creator->name, $createdUser->creator->name);
    }

    /** @test */
    public function it_has_created_users_relationship()
    {
        // Create a creator user
        $creator = User::create([
            'name' => 'Creator',
            'email' => 'creator@example.com',
            'password' => bcrypt('password'),
        ]);

        // Create a user type
        $userType = UserType::create([
            'name' => 'Contractor User',
            'description' => 'Contractor User',
            'is_hidden' => false,
        ]);

        // Create users created by the creator
        $createdUser1 = User::create([
            'name' => 'Created User 1',
            'email' => 'created1@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
            'created_by' => $creator->id,
        ]);

        $createdUser2 = User::create([
            'name' => 'Created User 2',
            'email' => 'created2@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
            'created_by' => $creator->id,
        ]);

        // Test createdUsers relationship
        $this->assertCount(2, $creator->createdUsers);
        $this->assertTrue($creator->createdUsers->contains($createdUser1));
        $this->assertTrue($creator->createdUsers->contains($createdUser2));
    }

    /** @test */
    public function it_has_user_type_relationship()
    {
        // Create a user type
        $userType = UserType::create([
            'name' => 'Contractor Admin',
            'description' => 'Contractor Administrator',
            'is_hidden' => false,
        ]);

        // Create a user with that type
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'user_type_id' => $userType->id,
        ]);

        // Test userType relationship
        $this->assertEquals($userType->id, $user->userType->id);
        $this->assertEquals($userType->name, $user->userType->name);
    }

    /** @test */
    public function it_can_scope_active_users()
    {
        // Create users
        $activeUser = User::create([
            'name' => 'Active User',
            'email' => 'active@example.com',
            'password' => bcrypt('password'),
        ]);

        $deletedUser = User::create([
            'name' => 'Deleted User',
            'email' => 'deleted@example.com',
            'password' => bcrypt('password'),
        ]);

        // Soft delete one user
        $deletedUser->delete();

        // Test active scope
        $activeUsers = User::active()->get();
        $this->assertCount(1, $activeUsers);
        $this->assertEquals($activeUser->id, $activeUsers->first()->id);
    }
}