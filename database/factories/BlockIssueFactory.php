<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\BlockUnit;
use App\Models\Priority;
use App\Models\IssueStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockIssue>
 */
class BlockIssueFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ref_no' => $this->faker->unique()->numerify('ISS-####'),
            'block_id' => Block::factory(),
            'priority' => $this->faker->randomElement(['Low', 'Normal', 'High', 'Urgent', 'Critical']),
            'status' => $this->faker->randomElement(['Open', 'In Progress', 'Resolved', 'Closed']),
            'assigned_to' => User::factory(),
            'reported_by' => $this->faker->name(),
            'issued_from' => $this->faker->randomElement([1, 2, 3]),
            'from_id' => $this->faker->numberBetween(1, 10),
            'contractor_type_id' => $this->faker->numberBetween(1, 5),
            'priority_id' => Priority::factory(),
            'block_unit_id' => BlockUnit::factory(),
            'contact_details' => $this->faker->paragraph(),
            'issue' => $this->faker->sentence(6),
            'issue_type' => $this->faker->randomElement(['Maintenance', 'Repair', 'Inspection', 'Emergency']),
            'issue_details' => $this->faker->paragraph(3),
            'contact_name' => $this->faker->name(),
            'contact_mobile' => $this->faker->phoneNumber(),
            'contact_email' => $this->faker->email(),
            'contact_method_id' => $this->faker->numberBetween(1, 3),
            'salutation' => $this->faker->randomElement(['Mr.', 'Ms.', 'Dr.', 'Prof.']),
            'phone_number' => $this->faker->phoneNumber(),
            'preferred_start_date_time' => $this->faker->dateTimeBetween('now', '+1 month'),
            'preferred_end_date_time' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'note_for_access' => $this->faker->paragraph(),
            'issued_by' => User::factory(),
            'block_visit_id' => null,
            'block_inspection_id' => null,
            'issued_date_time' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'comment' => $this->faker->paragraph(),
            'is_mobile' => $this->faker->boolean(),
            'created_by' => User::factory(),
            'updated_by' => null,
            'issue_status_id' => IssueStatus::factory(),
        ];
    }

    /**
     * Create an issue with specific status.
     */
    public function withStatus($statusId): static
    {
        return $this->state(fn (array $attributes) => [
            'issue_status_id' => $statusId,
        ]);
    }

    /**
     * Create an issue with specific priority.
     */
    public function withPriority($priorityId): static
    {
        return $this->state(fn (array $attributes) => [
            'priority_id' => $priorityId,
        ]);
    }
}
