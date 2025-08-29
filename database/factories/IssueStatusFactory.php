<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\IssueStatus>
 */
class IssueStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->randomElement(['Open', 'In Progress', 'Resolved', 'Closed', 'On Hold']),
            'value' => $this->faker->numberBetween(1, 5),
            'btn_class' => $this->faker->randomElement(['warning', 'info', 'success', 'secondary', 'danger']),
            'description' => $this->faker->sentence(),
        ];
    }

    /**
     * Create an open status
     */
    public function open(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Open',
            'value' => 1,
            'btn_class' => 'warning',
            'description' => 'Issue has been reported and is awaiting attention',
        ]);
    }

    /**
     * Create an in progress status
     */
    public function inProgress(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'In Progress',
            'value' => 2,
            'btn_class' => 'info',
            'description' => 'Issue is currently being worked on',
        ]);
    }

    /**
     * Create a resolved status
     */
    public function resolved(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Resolved',
            'value' => 3,
            'btn_class' => 'success',
            'description' => 'Issue has been resolved successfully',
        ]);
    }

    /**
     * Create a closed status
     */
    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Closed',
            'value' => 4,
            'btn_class' => 'secondary',
            'description' => 'Issue has been closed and documented',
        ]);
    }

    /**
     * Create an on hold status
     */
    public function onHold(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'On Hold',
            'value' => 5,
            'btn_class' => 'danger',
            'description' => 'Issue is temporarily on hold',
        ]);
    }
}

