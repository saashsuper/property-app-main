<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\BlockIssue;
use App\Models\BlockUnit;
use App\Models\BlockBuilding;
use App\Models\Priority;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockWorkOrder>
 */
class BlockWorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'block_id' => Block::factory(),
            'block_issue_id' => BlockIssue::factory(),
            'issued_from' => $this->faker->randomElement([1, 2, 3]),
            'from_id' => $this->faker->numberBetween(1, 10),
            'block_unit_id' => BlockUnit::factory(),
            'block_building_id' => BlockBuilding::factory(),
            'priority_id' => Priority::factory(),
            'issued_date_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'contractor_id' => User::factory(),
            'contact_name' => $this->faker->name(),
            'contact_mobile' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
            'preferred_start_date_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'preferred_end_date_time' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'deadline_date' => $this->faker->dateTimeBetween('+1 week', '+1 month'),
            'issued_by' => User::factory(),
            'status' => $this->faker->numberBetween(1, 5), // 1=Pending, 2=In Progress, 3=Completed, 4=Cancelled, 5=On Hold
            'ref_no' => $this->faker->unique()->numerify('WO-####'),
            'repair_category_id' => $this->faker->numberBetween(1, 10),
            'issue' => $this->faker->sentence(6),
            'note_for_access' => $this->faker->paragraph(),
            'pdf_path' => null,
            'pdf_name' => null,
            'block_visit_id' => null,
            'block_inspection_id' => null,
            'comment' => $this->faker->paragraph(),
            'is_mobile' => $this->faker->boolean(),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    /**
     * Create a work order with specific status.
     */
    public function withStatus($status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Create a work order assigned to specific contractor.
     */
    public function assignedTo($contractorId): static
    {
        return $this->state(fn (array $attributes) => [
            'contractor_id' => $contractorId,
        ]);
    }

    /**
     * Create a completed work order.
     */
    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 3, // Completed
        ]);
    }

    /**
     * Create a pending work order.
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 1, // Pending
        ]);
    }

    /**
     * Create an in-progress work order.
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 2, // In Progress
        ]);
    }
}
