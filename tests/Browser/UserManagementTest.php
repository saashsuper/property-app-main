<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use App\Models\User;
use App\Models\UserType;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserManagementTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_can_view_users_list()
    {
        // Create some test users
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->browse(function (Browser $browser) use ($user1, $user2) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('Users List')
                    ->assertSee('John Doe')
                    ->assertSee('Jane Smith')
                    ->assertSee('john@example.com')
                    ->assertSee('jane@example.com');
        });
    }

    public function test_admin_can_create_new_user()
    {
        $userType = UserType::factory()->create(['name' => 'Test User Type']);

        $this->browse(function (Browser $browser) use ($userType) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('Users List')
                    
                    // Click Add New User button
                    ->clickLink('Add New User')
                    ->assertPathIs('/users/create')
                    ->assertSee('Create User')
                    
                    // Fill in user details
                    ->type('name', 'Test User')
                    ->type('email', 'testuser@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('user_type_id', $userType->id)
                    
                    // Submit the form
                    ->press('Create User')
                    ->assertPathIs('/users')
                    ->assertSee('User created successfully')
                    ->assertSee('Test User')
                    ->assertSee('testuser@example.com');
        });
    }

    public function test_admin_can_edit_existing_user()
    {
        $user = User::factory()->create(['name' => 'Original Name', 'email' => 'original@example.com']);
        $userType = UserType::factory()->create(['name' => 'Updated User Type']);

        $this->browse(function (Browser $browser) use ($user, $userType) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('Original Name')
                    
                    // Click Edit button for the user
                    ->click('@edit-user-' . $user->id)
                    ->assertPathIs('/users/' . $user->id . '/edit')
                    ->assertSee('Edit User')
                    
                    // Update user details
                    ->clear('name')
                    ->type('name', 'Updated Name')
                    ->clear('email')
                    ->type('email', 'updated@example.com')
                    ->select('user_type_id', $userType->id)
                    
                    // Submit the form
                    ->press('Update User')
                    ->assertPathIs('/users')
                    ->assertSee('User updated successfully')
                    ->assertSee('Updated Name')
                    ->assertSee('updated@example.com');
        });
    }

    public function test_admin_can_delete_user()
    {
        $user = User::factory()->create(['name' => 'User To Delete', 'email' => 'delete@example.com']);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('User To Delete')
                    
                    // Click Delete button for the user
                    ->click('@delete-user-' . $user->id)
                    ->whenAvailable('.modal', function ($modal) {
                        $modal->assertSee('Are you sure you want to delete this user?')
                              ->press('Delete');
                    })
                    ->assertSee('User deleted successfully')
                    ->assertDontSee('User To Delete');
        });
    }

    public function test_user_creation_form_validation()
    {
        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/users/create')
                    ->assertSee('Create User')
                    
                    // Try to submit empty form
                    ->press('Create User')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required')
                    ->assertSee('The user type id field is required');
        });
    }

    public function test_user_creation_with_invalid_email()
    {
        $userType = UserType::factory()->create();

        $this->browse(function (Browser $browser) use ($userType) {
            $browser->loginAs($this->admin)
                    ->visit('/users/create')
                    ->assertSee('Create User')
                    
                    // Fill in form with invalid email
                    ->type('name', 'Test User')
                    ->type('email', 'invalid-email')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->select('user_type_id', $userType->id)
                    
                    // Submit the form
                    ->press('Create User')
                    ->assertSee('The email must be a valid email address');
        });
    }

    public function test_user_creation_with_password_mismatch()
    {
        $userType = UserType::factory()->create();

        $this->browse(function (Browser $browser) use ($userType) {
            $browser->loginAs($this->admin)
                    ->visit('/users/create')
                    ->assertSee('Create User')
                    
                    // Fill in form with mismatched passwords
                    ->type('name', 'Test User')
                    ->type('email', 'test@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'differentpassword')
                    ->select('user_type_id', $userType->id)
                    
                    // Submit the form
                    ->press('Create User')
                    ->assertSee('The password confirmation does not match');
        });
    }

    public function test_user_search_functionality()
    {
        // Create users with different names
        $user1 = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
        $user2 = User::factory()->create(['name' => 'Jane Smith', 'email' => 'jane@example.com']);

        $this->browse(function (Browser $browser) use ($user1, $user2) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('John Doe')
                    ->assertSee('Jane Smith')
                    
                    // Search for specific user
                    ->type('input[name="search"]', 'John')
                    ->press('Search')
                    ->assertSee('John Doe')
                    ->assertDontSee('Jane Smith')
                    
                    // Clear search
                    ->clear('input[name="search"]')
                    ->press('Search')
                    ->assertSee('John Doe')
                    ->assertSee('Jane Smith');
        });
    }

    public function test_user_pagination_works()
    {
        // Create more users than the pagination limit
        User::factory()->count(25)->create();

        $this->browse(function (Browser $browser) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('Users List')
                    
                    // Check if pagination is present
                    ->assertPresent('.pagination')
                    
                    // Navigate to next page
                    ->clickLink('Next')
                    ->assertSee('Users List')
                    
                    // Navigate back to first page
                    ->clickLink('Previous')
                    ->assertSee('Users List');
        });
    }

    public function test_user_type_filter_works()
    {
        $userType1 = UserType::factory()->create(['name' => 'Admin']);
        $userType2 = UserType::factory()->create(['name' => 'User']);
        
        $user1 = User::factory()->create(['user_type_id' => $userType1->id, 'name' => 'Admin User']);
        $user2 = User::factory()->create(['user_type_id' => $userType2->id, 'name' => 'Regular User']);

        $this->browse(function (Browser $browser) use ($userType1, $userType2, $user1, $user2) {
            $browser->loginAs($this->admin)
                    ->visit('/users')
                    ->assertSee('Admin User')
                    ->assertSee('Regular User')
                    
                    // Filter by user type
                    ->select('user_type_id', $userType1->id)
                    ->press('Filter')
                    ->assertSee('Admin User')
                    ->assertDontSee('Regular User')
                    
                    // Clear filter
                    ->select('user_type_id', '')
                    ->press('Filter')
                    ->assertSee('Admin User')
                    ->assertSee('Regular User');
        });
    }

    public function test_contractor_admin_restrictions()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $contractorUser = User::factory()->contractorUser()->create(['created_by' => $contractorAdmin->id]);
        $otherUser = User::factory()->create(['name' => 'Other User']);

        $this->browse(function (Browser $browser) use ($contractorAdmin, $contractorUser, $otherUser) {
            $browser->loginAs($contractorAdmin)
                    ->visit('/users')
                    ->assertSee('Users List')
                    
                    // Should only see users they created
                    ->assertSee($contractorUser->name)
                    ->assertDontSee($otherUser->name)
                    
                    // Should not see user type filter
                    ->assertDontSee('User Type')
                    
                    // Should not see edit/delete buttons for other users
                    ->assertDontSee('@edit-user-' . $otherUser->id)
                    ->assertDontSee('@delete-user-' . $otherUser->id);
        });
    }
}
