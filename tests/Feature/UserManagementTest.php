<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserManagementTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types for testing
        $this->adminType = UserType::factory()->admin()->create();
        $this->contractorAdminType = UserType::factory()->contractorAdmin()->create();
        $this->contractorUserType = UserType::factory()->contractorUser()->create();
    }

    /** @test */
    public function admin_can_view_all_users()
    {
        // Create admin user
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create some test users
        $user1 = User::factory()->contractorUser()->create();
        $user2 = User::factory()->contractorAdmin()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/users');
        
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertSee($user2->name);
    }

    /** @test */
    public function contractor_admin_can_only_view_users_they_created()
    {
        // Create contractor admin
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create users - some by contractor admin, some by others
        $user1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $user2 = User::factory()->contractorUser()->create(); // Created by someone else
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/users');
        
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertDontSee($user2->name);
    }

    /** @test */
    public function admin_can_create_any_user_type()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type_id' => $this->contractorUserType->id,
        ];
        
        $response = $this->post('/users', $userData);
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User created successfully!');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_type_id' => $this->contractorUserType->id,
            'created_by' => $admin->id,
        ]);
    }

    /** @test */
    public function contractor_admin_can_only_create_contractor_users()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        $this->actingAs($contractorAdmin);
        
        // Try to create a contractor user (should work)
        $userData = [
            'name' => 'Test Contractor User',
            'email' => 'contractor@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type_id' => $this->contractorUserType->id,
        ];
        
        $response = $this->post('/users', $userData);
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User created successfully!');
        
        $this->assertDatabaseHas('users', [
            'name' => 'Test Contractor User',
            'email' => 'contractor@example.com',
            'user_type_id' => $this->contractorUserType->id,
            'created_by' => $contractorAdmin->id,
        ]);
    }

    /** @test */
    public function contractor_admin_cannot_create_non_contractor_users()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        $this->actingAs($contractorAdmin);
        
        // Try to create an admin user (should fail)
        $userData = [
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type_id' => $this->adminType->id,
        ];
        
        $response = $this->post('/users', $userData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['user_type_id']);
        
        $this->assertDatabaseMissing('users', [
            'email' => 'admin@example.com',
        ]);
    }

    /** @test */
    public function contractor_admin_can_create_contractor_admin_with_web_login()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        $this->actingAs($contractorAdmin);
        
        $userData = [
            'name' => 'New Contractor Admin',
            'email' => 'newadmin@example.com',
            'user_type_id' => $this->contractorAdminType->id,
            'is_web_login_required' => 'on',
        ];
        
        $response = $this->post('/users', $userData);
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User created successfully!');
        
        $user = User::where('email', 'newadmin@example.com')->first();
        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /** @test */
    public function user_creation_requires_valid_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->post('/users', []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name', 'email', 'user_type_id']);
    }

    /** @test */
    public function user_creation_requires_unique_email()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $existingUser = User::factory()->create(['email' => 'existing@example.com']);
        
        $this->actingAs($admin);
        
        $userData = [
            'name' => 'Test User',
            'email' => 'existing@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type_id' => $this->contractorUserType->id,
        ];
        
        $response = $this->post('/users', $userData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }

    /** @test */
    public function admin_can_edit_any_user()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user = User::factory()->contractorUser()->create();
        
        $this->actingAs($admin);
        
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'user_type_id' => $this->adminType->id,
        ];
        
        $response = $this->put("/users/{$user->id}", $updateData);
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User updated successfully!');
        
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('updated@example.com', $user->email);
    }

    /** @test */
    public function contractor_admin_can_only_edit_users_they_created()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $user1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $user2 = User::factory()->contractorUser()->create(); // Created by someone else
        
        $this->actingAs($contractorAdmin);
        
        // Should be able to edit user1
        $response = $this->get("/users/{$user1->id}/edit");
        $response->assertStatus(200);
        
        // Should not be able to edit user2
        $response = $this->get("/users/{$user2->id}/edit");
        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'You can only edit users you created.');
    }

    /** @test */
    public function contractor_admin_cannot_assign_non_contractor_user_types()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $user = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $updateData = [
            'name' => $user->name,
            'email' => $user->email,
            'user_type_id' => $this->adminType->id, // Try to assign admin type
        ];
        
        $response = $this->put("/users/{$user->id}", $updateData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['user_type_id']);
    }

    /** @test */
    public function admin_can_delete_any_user()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user = User::factory()->contractorUser()->create();
        
        $this->actingAs($admin);
        
        $response = $this->delete("/users/{$user->id}");
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User deleted successfully!');
        
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    /** @test */
    public function contractor_admin_can_only_delete_users_they_created()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $user1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $user2 = User::factory()->contractorUser()->create(); // Created by someone else
        
        $this->actingAs($contractorAdmin);
        
        // Should be able to delete user1
        $response = $this->delete("/users/{$user1->id}");
        $response->assertRedirect('/users');
        $response->assertSessionHas('success', 'User deleted successfully!');
        
        // Should not be able to delete user2
        $response = $this->delete("/users/{$user2->id}");
        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'You can only delete users you created.');
    }

    /** @test */
    public function user_cannot_delete_their_own_account()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->delete("/users/{$admin->id}");
        
        $response->assertRedirect('/users');
        $response->assertSessionHas('error', 'Cannot delete your own account.');
        
        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    /** @test */
    public function user_search_functionality_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);
        
        $this->actingAs($admin);
        
        // Search by name
        $response = $this->get('/users?search=John');
        $response->assertStatus(200);
        $response->assertSee('John Doe');
        $response->assertDontSee('Jane Smith');
        
        // Search by email
        $response = $this->get('/users?search=jane@example.com');
        $response->assertStatus(200);
        $response->assertSee('Jane Smith');
        $response->assertDontSee('John Doe');
    }

    /** @test */
    public function user_type_filter_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user1 = User::factory()->contractorUser()->create();
        $user2 = User::factory()->contractorAdmin()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/users?user_type_id={$this->contractorUserType->id}");
        $response->assertStatus(200);
        $response->assertSee($user1->name);
        $response->assertDontSee($user2->name);
    }

    /** @test */
    public function api_endpoint_returns_users_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user = User::factory()->contractorUser()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/users');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'email',
                    'user_type',
                ]
            ]
        ]);
    }

    /** @test */
    public function api_endpoint_returns_specific_user_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $user = User::factory()->contractorUser()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/api/users/{$user->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }
}
