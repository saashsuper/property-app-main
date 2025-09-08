<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\UserType;
use App\Models\WorkOrder;
use App\Models\WorkOrderImage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class WorkOrderApiTest extends TestCase
{
    use RefreshDatabase;

    protected $adminType;
    protected $contractorAdminType;
    protected $contractorUserType;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create user types
        $this->adminType = UserType::factory()->admin()->create();
        $this->contractorAdminType = UserType::factory()->contractorAdmin()->create();
        $this->contractorUserType = UserType::factory()->contractorUser()->create();
        
        // Fake storage for file uploads
        Storage::fake('public');
    }

    /** @test */
    public function work_orders_api_requires_authentication()
    {
        $response = $this->getJson('/api/work-orders');
        $response->assertUnauthorized();
    }

    /** @test */
    public function api_returns_work_orders_json_structure()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders
        $workOrder1 = WorkOrder::factory()->active()->create();
        $workOrder2 = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->getJson('/api/work-orders');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                '*' => [
                    'id',
                    'code',
                    'fault_description',
                    'priority',
                    'priority_label',
                    'user'
                ]
            ]
        ]);
        
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertCount(2, $data['data']);
    }

    /** @test */
    public function api_returns_specific_work_order_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->getJson("/api/work-orders/{$workOrder->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'code',
                'fault_description',
                'priority',
                'priority_label',
                'user',
                'images'
            ]
        ]);
        
        $data = $response->json();
        $this->assertTrue($data['success']);
        $this->assertEquals($workOrder->id, $data['data']['id']);
    }

    /** @test */
    public function work_order_can_be_created_via_api()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $workOrderData = [
            'code' => 'WO-TEST-001',
            'property_id' => 1,
            'contractor_id' => 1,
            'priority' => 3,
            'priority_label' => 'High',
            'fault_description' => 'Test fault description',
            'issue_category' => 'Plumbing',
            'issue_type' => 'Repair',
            'issued_date' => '2024-01-01',
            'deadline' => '2024-01-15',
            'pricing' => 'Fixed',
            'contact_name' => 'Test Contact',
            'contact_number' => '1234567890',
            'contact_email' => 'test@example.com',
            'preferred_day' => 'Monday',
            'time_from' => '09:00',
            'time_to' => '17:00',
            'note' => 'Test note',
            'report' => 'Test report',
            'type' => 'Maintenance',
            'type_id' => 1,
        ];
        
        $response = $this->postJson('/work-orders', $workOrderData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order created successfully!');
        
        $this->assertDatabaseHas('work_orders', [
            'code' => 'WO-TEST-001',
            'fault_description' => 'Test fault description',
            'created_by' => $admin->id,
            'user_id' => $admin->id,
        ]);
    }

    /** @test */
    public function work_order_creation_requires_valid_data()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $response = $this->postJson('/work-orders', []);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['code', 'property_id', 'contractor_id', 'priority', 'priority_label', 'fault_description', 'issue_category', 'issue_type', 'issued_date', 'deadline', 'pricing', 'type', 'type_id']);
    }

    /** @test */
    public function work_order_code_must_be_unique()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create existing work order
        WorkOrder::factory()->create(['code' => 'WO-DUPLICATE']);
        
        $this->actingAs($admin);
        
        $workOrderData = [
            'code' => 'WO-DUPLICATE',
            'property_id' => 1,
            'contractor_id' => 1,
            'priority' => 3,
            'priority_label' => 'High',
            'fault_description' => 'Test fault description',
            'issue_category' => 'Plumbing',
            'issue_type' => 'Repair',
            'issued_date' => '2024-01-01',
            'deadline' => '2024-01-15',
            'pricing' => 'Fixed',
            'type' => 'Maintenance',
            'type_id' => 1,
        ];
        
        $response = $this->postJson('/work-orders', $workOrderData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function work_order_can_be_updated_via_api()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        $this->actingAs($admin);
        
        $updateData = [
            'code' => $workOrder->code,
            'property_id' => 2,
            'contractor_id' => 2,
            'priority' => 4,
            'priority_label' => 'Urgent',
            'fault_description' => 'Updated fault description',
            'issue_category' => 'Electrical',
            'issue_type' => 'Installation',
            'issued_date' => '2024-01-02',
            'deadline' => '2024-01-16',
            'pricing' => 'Quote',
            'type' => 'Repair',
            'type_id' => 2,
        ];
        
        $response = $this->putJson("/work-orders/{$workOrder->id}", $updateData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order updated successfully!');
        
        $workOrder->refresh();
        $this->assertEquals('Updated fault description', $workOrder->fault_description);
        $this->assertEquals('Electrical', $workOrder->issue_category);
        $this->assertEquals($admin->id, $workOrder->updated_by);
    }

    /** @test */
    public function work_order_can_be_deleted_via_api()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->deleteJson("/work-orders/{$workOrder->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order deleted successfully!');
        
        $this->assertDatabaseMissing('work_orders', ['id' => $workOrder->id]);
    }

    /** @test */
    public function work_order_deletion_removes_associated_images()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        // Create images for the work order
        $image1 = WorkOrderImage::factory()->create(['work_order_id' => $workOrder->id]);
        $image2 = WorkOrderImage::factory()->create(['work_order_id' => $workOrder->id]);
        
        $this->actingAs($admin);
        
        $response = $this->deleteJson("/work-orders/{$workOrder->id}");
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order deleted successfully!');
        
        $this->assertDatabaseMissing('work_orders', ['id' => $workOrder->id]);
        $this->assertDatabaseMissing('work_order_images', ['id' => $image1->id]);
        $this->assertDatabaseMissing('work_order_images', ['id' => $image2->id]);
    }

    /** @test */
    public function contractor_admin_can_reassign_work_order()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $contractorUser = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        // Create work order assigned to contractor admin
        $workOrder = WorkOrder::factory()->active()->assignedTo($contractorAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->postJson("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => $contractorUser->id
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order reassigned successfully!');
        
        $workOrder->refresh();
        $this->assertEquals($contractorUser->id, $workOrder->user_id);
        $this->assertEquals($contractorAdmin->id, $workOrder->updated_by);
    }

    /** @test */
    public function contractor_admin_cannot_reassign_work_order_not_assigned_to_them()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $otherUser = User::factory()->create();
        $contractorUser = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        // Create work order assigned to other user
        $workOrder = WorkOrder::factory()->active()->assignedTo($otherUser->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->postJson("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => $contractorUser->id
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You can only reassign work orders assigned to you.');
        
        $workOrder->refresh();
        $this->assertEquals($otherUser->id, $workOrder->user_id); // Should remain unchanged
    }

    /** @test */
    public function contractor_admin_cannot_reassign_to_user_not_created_by_them()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $otherAdmin = User::factory()->admin()->create();
        $otherContractorUser = User::factory()->contractorUser()->createdBy($otherAdmin->id)->create();
        
        // Create work order assigned to contractor admin
        $workOrder = WorkOrder::factory()->active()->assignedTo($contractorAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->postJson("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => $otherContractorUser->id
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You can only reassign to contractor users you created.');
        
        $workOrder->refresh();
        $this->assertEquals($contractorAdmin->id, $workOrder->user_id); // Should remain unchanged
    }

    /** @test */
    public function non_contractor_admin_cannot_reassign_work_orders()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->postJson("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => $admin->id
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHas('error', 'You do not have permission to reassign work orders.');
    }

    /** @test */
    public function work_order_reassignment_requires_valid_user()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $workOrder = WorkOrder::factory()->active()->assignedTo($contractorAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->postJson("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => 99999 // Non-existent user
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['new_user_id']);
    }

    /** @test */
    public function work_order_model_relationships_work_correctly()
    {
        $user = User::factory()->create();
        $workOrder = WorkOrder::factory()->create([
            'user_id' => $user->id,
            'created_by' => $user->id,
        ]);
        
        // Test user relationship
        $this->assertEquals($user->id, $workOrder->user->id);
        
        // Test creator relationship
        $this->assertEquals($user->id, $workOrder->creator->id);
        
        // Test images relationship
        $image = WorkOrderImage::factory()->create(['work_order_id' => $workOrder->id]);
        $this->assertCount(1, $workOrder->images);
        $this->assertEquals($image->id, $workOrder->images->first()->id);
    }

    /** @test */
    public function work_order_scope_active_works_correctly()
    {
        $activeWorkOrder = WorkOrder::factory()->active()->create();
        $inactiveWorkOrder = WorkOrder::factory()->withStatus(2)->create();
        
        $activeWorkOrders = WorkOrder::active()->get();
        
        $this->assertCount(1, $activeWorkOrders);
        $this->assertEquals($activeWorkOrder->id, $activeWorkOrders->first()->id);
    }

    /** @test */
    public function work_order_priority_and_status_attributes_work()
    {
        $workOrder = WorkOrder::factory()->create([
            'priority_label' => 'High',
            'common_status_id' => 1,
        ]);
        
        $this->assertEquals('High', $workOrder->priority_text);
        $this->assertEquals('Active', $workOrder->status_text);
    }
}
