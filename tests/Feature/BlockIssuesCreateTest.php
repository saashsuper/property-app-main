<?php

namespace Tests\Feature;

use App\Models\Block;
use App\Models\BlockUnit;
use App\Models\ContactMethod;
use App\Models\User;
use App\Models\UserType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class BlockIssuesCreateTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_create_block_issue_with_valid_unit(): void
    {
        // Create a user to act as (reporter)
        $reporter = User::factory()->create();
        $this->actingAs($reporter);

        // Ensure a Property manager user type exists
        $managerType = UserType::firstOrCreate(['name' => 'Property manager']);
        $assignee = User::factory()->create();
        $assignee->user_type_id = $managerType->id;
        $assignee->save();

        // Create block and a unit under it
        $block = Block::factory()->create();
        $unit = BlockUnit::factory()->create([
            'block_id' => $block->id,
        ]);

        // Create a contact method
        $contactMethod = ContactMethod::factory()->create();

        // Prepare payload as from the create page
        $payload = [
            'block_id' => $block->id,
            // Unit selection uses hidden field named block_unit_id
            'block_unit_id' => $unit->id,
            'assigned_to' => $assignee->id,
            'contact_method_id' => $contactMethod->id,
            'priority_id' => 2,
            'issue_type' => 'plumbing',
            'issue' => 'Leaking pipe in the basement',
            'contact_email' => 'reporter@example.com',
            'contact_details' => 'Phone: 123456789',
            'issue_details' => 'Water is pooling near the main valve.',
            'default_contact_details' => 'Default contact information will be used',
        ];

        $response = $this->post(route('block-issues.store'), $payload);

        $response->assertStatus(302); // redirect on success
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }
}


