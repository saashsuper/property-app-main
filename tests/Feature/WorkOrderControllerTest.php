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

class WorkOrderControllerTest extends TestCase
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
    public function work_orders_index_requires_authentication()
    {
        $response = $this->get('/work-orders');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function admin_can_view_all_work_orders()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders assigned to different users
        $workOrder1 = WorkOrder::factory()->active()->create();
        $workOrder2 = WorkOrder::factory()->active()->create();
        $workOrder3 = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/work-orders');
        
        $response->assertStatus(200);
        $response->assertViewHas('workOrders');
        $response->assertViewHas('isContractorAdmin', false);
        
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(3, $workOrders);
    }

    /** @test */
    public function contractor_admin_can_only_view_assigned_work_orders()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        $otherUser = User::factory()->create();
        
        // Create work orders - some assigned to contractor admin, some to others
        $assignedWorkOrder1 = WorkOrder::factory()->active()->assignedTo($contractorAdmin->id)->create();
        $assignedWorkOrder2 = WorkOrder::factory()->active()->assignedTo($contractorAdmin->id)->create();
        $otherWorkOrder = WorkOrder::factory()->active()->assignedTo($otherUser->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/work-orders');
        
        $response->assertStatus(200);
        $response->assertViewHas('workOrders');
        $response->assertViewHas('isContractorAdmin', true);
        
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(2, $workOrders);
        
        // Check that only assigned work orders are visible
        $workOrderIds = $workOrders->pluck('id')->toArray();
        $this->assertContains($assignedWorkOrder1->id, $workOrderIds);
        $this->assertContains($assignedWorkOrder2->id, $workOrderIds);
        $this->assertNotContains($otherWorkOrder->id, $workOrderIds);
    }

    /** @test */
    public function work_orders_can_be_searched()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders with specific searchable content
        $workOrder1 = WorkOrder::factory()->active()->create([
            'code' => 'WO-1234',
            'fault_description' => 'Broken pipe in kitchen',
            'contact_name' => 'John Doe'
        ]);
        $workOrder2 = WorkOrder::factory()->active()->create([
            'code' => 'WO-5678',
            'fault_description' => 'Electrical issue in bedroom',
            'contact_name' => 'Jane Smith'
        ]);
        
        $this->actingAs($admin);
        
        // Search by code
        $response = $this->get('/work-orders?search=WO-1234');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($workOrder1->id, $workOrders->first()->id);
        
        // Search by fault description
        $response = $this->get('/work-orders?search=kitchen');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($workOrder1->id, $workOrders->first()->id);
        
        // Search by contact name
        $response = $this->get('/work-orders?search=Jane');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($workOrder2->id, $workOrders->first()->id);
    }

    /** @test */
    public function work_orders_can_be_filtered_by_priority()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders with different priorities
        $highPriorityWorkOrder = WorkOrder::factory()->active()->withPriority(5)->create();
        $lowPriorityWorkOrder = WorkOrder::factory()->active()->withPriority(1)->create();
        
        $this->actingAs($admin);
        
        // Filter by high priority
        $response = $this->get('/work-orders?priority=5');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($highPriorityWorkOrder->id, $workOrders->first()->id);
        
        // Filter by low priority
        $response = $this->get('/work-orders?priority=1');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($lowPriorityWorkOrder->id, $workOrders->first()->id);
    }

    /** @test */
    public function work_orders_can_be_filtered_by_status()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders with different statuses
        $activeWorkOrder = WorkOrder::factory()->active()->create();
        $inactiveWorkOrder = WorkOrder::factory()->withStatus(2)->create();
        
        $this->actingAs($admin);
        
        // Filter by active status
        $response = $this->get('/work-orders?status=1');
        $workOrders = $response->viewData('workOrders');
        $this->assertCount(1, $workOrders);
        $this->assertEquals($activeWorkOrder->id, $workOrders->first()->id);
    }

    /** @test */
    public function contractor_admin_can_view_contractor_users_for_reassignment()
    {
        $contractorAdmin = User::factory()->contractorAdmin()->create();
        
        // Create contractor users created by this admin
        $contractorUser1 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        $contractorUser2 = User::factory()->contractorUser()->createdBy($contractorAdmin->id)->create();
        
        // Create contractor user created by different admin (should not appear)
        $otherAdmin = User::factory()->admin()->create();
        $otherContractorUser = User::factory()->contractorUser()->createdBy($otherAdmin->id)->create();
        
        $this->actingAs($contractorAdmin);
        
        $response = $this->get('/work-orders');
        
        $response->assertStatus(200);
        $response->assertViewHas('contractorUsers');
        
        $contractorUsers = $response->viewData('contractorUsers');
        $this->assertCount(2, $contractorUsers);
        
        // Check that only users created by this admin are included
        $userIds = $contractorUsers->pluck('id')->toArray();
        $this->assertContains($contractorUser1->id, $userIds);
        $this->assertContains($contractorUser2->id, $userIds);
        $this->assertNotContains($otherContractorUser->id, $userIds);
    }

    /** @test */
    public function admin_can_create_work_order()
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
        
        $response = $this->post('/work-orders', $workOrderData);
        
        $response->assertRedirect('/work-orders');
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
        
        $response = $this->post('/work-orders', []);
        
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
        
        $response = $this->post('/work-orders', $workOrderData);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['code']);
    }

    /** @test */
    public function work_order_can_be_created_with_images()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        $this->actingAs($admin);
        
        $image1 = UploadedFile::fake()->image('test1.jpg');
        $image2 = UploadedFile::fake()->image('test2.jpg');
        
        $workOrderData = [
            'code' => 'WO-IMAGE-001',
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
            'images' => [$image1, $image2],
        ];
        
        $response = $this->post('/work-orders', $workOrderData);
        
        $response->assertRedirect('/work-orders');
        $response->assertSessionHas('success', 'Work order created successfully!');
        
        $workOrder = WorkOrder::where('code', 'WO-IMAGE-001')->first();
        $this->assertNotNull($workOrder);
        $this->assertCount(2, $workOrder->images);
        
        // Check that images were stored (using fake storage)
        Storage::disk('public')->assertExists('work-orders/' . $workOrder->images->first()->image);
        Storage::disk('public')->assertExists('work-orders/' . $workOrder->images->last()->image);
    }

    /** @test */
    public function admin_can_view_work_order_details()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/work-orders/{$workOrder->id}");
        
        $response->assertStatus(200);
        $response->assertViewHas('workOrder');
        
        $viewWorkOrder = $response->viewData('workOrder');
        $this->assertEquals($workOrder->id, $viewWorkOrder->id);
    }

    /** @test */
    public function admin_can_edit_work_order()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/work-orders/{$workOrder->id}/edit");
        
        $response->assertStatus(200);
        $response->assertViewHas('workOrder');
        
        $viewWorkOrder = $response->viewData('workOrder');
        $this->assertEquals($workOrder->id, $viewWorkOrder->id);
    }

    /** @test */
    public function admin_can_update_work_order()
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
            'pricing' => 'Hourly',
            'type' => 'Repair',
            'type_id' => 2,
        ];
        
        $response = $this->put("/work-orders/{$workOrder->id}", $updateData);
        
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Work order updated successfully!');
        
        $workOrder->refresh();
        $this->assertEquals('Updated fault description', $workOrder->fault_description);
        $this->assertEquals('Electrical', $workOrder->issue_category);
        $this->assertEquals($admin->id, $workOrder->updated_by);
    }

    /** @test */
    public function admin_can_delete_work_order()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->create();
        
        $this->actingAs($admin);
        
        $response = $this->delete("/work-orders/{$workOrder->id}");
        
        $response->assertRedirect('/work-orders');
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
        
        $response = $this->delete("/work-orders/{$workOrder->id}");
        
        $response->assertRedirect('/work-orders');
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
        
        $response = $this->post("/work-orders/{$workOrder->id}/reassign", [
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
        
        $response = $this->post("/work-orders/{$workOrder->id}/reassign", [
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
        
        $response = $this->post("/work-orders/{$workOrder->id}/reassign", [
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
        
        $response = $this->post("/work-orders/{$workOrder->id}/reassign", [
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
        
        $response = $this->post("/work-orders/{$workOrder->id}/reassign", [
            'new_user_id' => 99999 // Non-existent user
        ]);
        
        $response->assertRedirect();
        $response->assertSessionHasErrors(['new_user_id']);
    }

    /** @test */
    public function api_endpoint_returns_work_orders_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        
        // Create work orders
        $workOrder1 = WorkOrder::factory()->active()->create();
        $workOrder2 = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get('/api/work-orders');
        
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
    public function api_endpoint_returns_specific_work_order_json()
    {
        $admin = User::factory()->withUserType($this->adminType->id)->create();
        $workOrder = WorkOrder::factory()->active()->create();
        
        $this->actingAs($admin);
        
        $response = $this->get("/api/work-orders/{$workOrder->id}");
        
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
    public function api_endpoints_require_authentication()
    {
        $workOrder = WorkOrder::factory()->active()->create();
        
        $response = $this->get('/api/work-orders');
        $response->assertRedirect('/login');
        
        $response = $this->get("/api/work-orders/{$workOrder->id}");
        $response->assertRedirect('/login');
    }
}
