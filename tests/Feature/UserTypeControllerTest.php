<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTypeControllerTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types for testing
        $this->adminType = UserType::factory()->admin()->create();
        $this->contractorAdminType = UserType::factory()->contractorAdmin()->create();
        $this->contractorUserType = UserType::factory()->contractorUser()->create();
        $this->financialAdminType = UserType::factory()->financialAdmin()->create();
    }

    /** @test */
    public function admin_can_view_all_user_types()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/user-types');
        
        $response->assertStatus(200);
        $response->assertSee($this->adminType->name);
        $response->assertSee($this->contractorAdminType->name);
        $response->assertSee($this->contractorUserType->name);
        $response->assertSee($this->financialAdminType->name);
    }

    /** @test */
    public function admin_can_create_user_type()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $userTypeData = [
            'name' => 'Test User Type',
            'description' => 'Test description for user type',
            'visible' => 'on', // This means is_hidden = false
        ];
        
        $response = $this->post('/user-types', $userTypeData);
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type created successfully!');
        
        $this->assertDatabaseHas('user_types', [
            'name' => 'Test User Type',
            'description' => 'Test description for user type',
            'is_hidden' => false,
        ]);
    }

    /** @test */
    public function admin_can_create_hidden_user_type()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $userTypeData = [
            'name' => 'Hidden User Type',
            'description' => 'This user type is hidden',
            // No 'visible' checkbox means is_hidden = true
        ];
        
        $response = $this->post('/user-types', $userTypeData);
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type created successfully!');
        
        $this->assertDatabaseHas('user_types', [
            'name' => 'Hidden User Type',
            'description' => 'This user type is hidden',
            'is_hidden' => true,
        ]);
    }

    /** @test */
    public function user_type_creation_requires_valid_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->post('/user-types', []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function user_type_creation_requires_unique_name()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $userTypeData = [
            'name' => $this->adminType->name, // Duplicate name
            'description' => 'Test description',
        ];
        
        $response = $this->post('/user-types', $userTypeData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function admin_can_update_user_type()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create([
            'name' => 'Original Name',
            'description' => 'Original description',
            'is_hidden' => false,
        ]);
        
        $this->actingAs($admin);
        
        $updateData = [
            'name' => 'Updated Name',
            'description' => 'Updated description',
            'visible' => 'on', // Keep visible
        ];
        
        $response = $this->put("/user-types/{$userType->id}", $updateData);
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type updated successfully!');
        
        $userType->refresh();
        $this->assertEquals('Updated Name', $userType->name);
        $this->assertEquals('Updated description', $userType->description);
        $this->assertFalse($userType->is_hidden);
    }

    /** @test */
    public function admin_can_hide_user_type()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create([
            'name' => 'Visible Type',
            'is_hidden' => false,
        ]);
        
        $this->actingAs($admin);
        
        $updateData = [
            'name' => 'Visible Type',
            'description' => $userType->description,
            // No 'visible' checkbox means hide it
        ];
        
        $response = $this->put("/user-types/{$userType->id}", $updateData);
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type updated successfully!');
        
        $userType->refresh();
        $this->assertTrue($userType->is_hidden);
    }

    /** @test */
    public function admin_can_delete_user_type_without_users()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->delete("/user-types/{$userType->id}");
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type deleted successfully!');
        
        $this->assertDatabaseMissing('user_types', ['id' => $userType->id]);
    }

    /** @test */
    public function admin_cannot_delete_user_type_with_associated_users()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create();
        
        // Create a user with this user type
        User::factory()->withUserType($userType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->delete("/user-types/{$userType->id}");
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('error', 'Cannot delete user type. It has associated users.');
        
        $this->assertDatabaseHas('user_types', ['id' => $userType->id]);
    }

    /** @test */
    public function user_type_search_functionality_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        // Search by name
        $response = $this->get('/user-types?search=Admin');
        $response->assertStatus(200);
        $response->assertSee('Admin');
        $response->assertDontSee('Contractor User');
        
        // Search by description
        $response = $this->get('/user-types?search=Administrator');
        $response->assertStatus(200);
        $response->assertSee('Admin');
    }

    /** @test */
    public function user_type_list_shows_user_count()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create();
        
        // Create users with this user type
        User::factory()->withUserType($userType->id)->count(3)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/user-types');
        $response->assertStatus(200);
        $response->assertSee('3'); // Should show user count
    }

    /** @test */
    public function api_endpoint_returns_user_types_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/user-types');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'is_hidden',
                    'users_count',
                ]
            ]
        ]);
    }

    /** @test */
    public function api_endpoint_returns_specific_user_type_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/api/user-types/{$userType->id}");
        
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'data' => [
                'id' => $userType->id,
                'name' => $userType->name,
                'description' => $userType->description,
            ]
        ]);
    }

    /** @test */
    public function user_type_visibility_scope_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create visible and hidden user types
        $visibleType = UserType::factory()->visible()->create();
        $hiddenType = UserType::factory()->hidden()->create();
        
        $this->actingAs($admin);
        
        // Test that both are visible in admin view (admin can see all)
        $response = $this->get('/user-types');
        $response->assertStatus(200);
        $response->assertSee($visibleType->name);
        $response->assertSee($hiddenType->name);
    }

    /** @test */
    public function user_type_name_validation_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        // Test empty name
        $response = $this->post('/user-types', [
            'name' => '',
            'description' => 'Test description',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
        
        // Test name too long
        $response = $this->post('/user-types', [
            'name' => str_repeat('a', 200), // Too long
            'description' => 'Test description',
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['name']);
    }

    /** @test */
    public function user_type_description_validation_works()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        // Test description too long
        $response = $this->post('/user-types', [
            'name' => 'Test Type',
            'description' => str_repeat('a', 200), // Too long
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['description']);
    }

    /** @test */
    public function user_type_update_preserves_visibility_when_not_changed()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $userType = UserType::factory()->create([
            'name' => 'Test Type',
            'is_hidden' => true,
        ]);
        
        $this->actingAs($admin);
        
        $updateData = [
            'name' => 'Updated Type',
            'description' => 'Updated description',
            // No visibility checkbox - should preserve hidden state
        ];
        
        $response = $this->put("/user-types/{$userType->id}", $updateData);
        
        $response->assertRedirect('/user-types');
        $response->assertSessionHas('success', 'User type updated successfully!');
        
        $userType->refresh();
        $this->assertEquals('Updated Type', $userType->name);
        $this->assertTrue($userType->is_hidden); // Should remain hidden
    }
}
