<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrder>
 */
class WorkOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->numerify('WO-####'),
            'property_id' => $this->faker->numberBetween(1, 100),
            'contractor_id' => $this->faker->numberBetween(1, 10),
            'user_id' => User::factory(),
            'priority' => $this->faker->numberBetween(1, 5),
            'priority_label' => $this->faker->randomElement(['Low', 'Normal', 'High', 'Urgent', 'Critical']),
            'fault_description' => $this->faker->paragraph(3),
            'issue_category' => $this->faker->randomElement(['Plumbing', 'Electrical', 'HVAC', 'General', 'Emergency']),
            'issue_type' => $this->faker->randomElement(['Repair', 'Installation', 'Inspection', 'Replacement', 'Maintenance']),
            'issued_date' => $this->faker->date('Y-m-d'),
            'deadline' => $this->faker->date('Y-m-d'),
            'pricing' => $this->faker->randomElement(['Fixed', 'Quote', 'TBD']),
            'contact_name' => $this->faker->name(),
            'contact_number' => $this->faker->numerify('###-###-####'),
            'contact_email' => $this->faker->email(),
            'preferred_day' => $this->faker->randomElement(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday']),
            'time_from' => $this->faker->time('H:i'),
            'time_to' => $this->faker->time('H:i'),
            'note' => $this->faker->sentence(5),
            'report' => $this->faker->sentence(10),
            'type' => $this->faker->randomElement(['Maintenance', 'Repair', 'Installation']),
            'type_id' => $this->faker->numberBetween(1, 10),
            'common_status_id' => $this->faker->numberBetween(1, 3),
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }

    /**
     * Create a work order with specific status.
     */
    public function withStatus($statusId): static
    {
        return $this->state(fn (array $attributes) => [
            'common_status_id' => $statusId,
        ]);
    }

    /**
     * Create an active work order.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'common_status_id' => 1,
        ]);
    }

    /**
     * Create a work order assigned to specific user.
     */
    public function assignedTo($userId): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $userId,
        ]);
    }

    /**
     * Create a work order with specific priority.
     */
    public function withPriority($priority): static
    {
        return $this->state(fn (array $attributes) => [
            'priority' => $priority,
        ]);
    }
}
