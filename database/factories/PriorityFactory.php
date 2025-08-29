<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Priority>
 */
class PriorityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'label' => $this->faker->randomElement(['Low', 'Normal', 'High', 'Urgent', 'Critical']),
            'value' => $this->faker->numberBetween(1, 5),
            'btn_class' => $this->faker->randomElement(['success', 'info', 'warning', 'danger', 'dark']),
        ];
    }

    /**
     * Create a low priority
     */
    public function low(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Low',
            'value' => 1,
            'btn_class' => 'success',
        ]);
    }

    /**
     * Create a normal priority
     */
    public function normal(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Normal',
            'value' => 2,
            'btn_class' => 'info',
        ]);
    }

    /**
     * Create a high priority
     */
    public function high(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'High',
            'value' => 3,
            'btn_class' => 'warning',
        ]);
    }

    /**
     * Create an urgent priority
     */
    public function urgent(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Urgent',
            'value' => 4,
            'btn_class' => 'danger',
        ]);
    }

    /**
     * Create a critical priority
     */
    public function critical(): static
    {
        return $this->state(fn (array $attributes) => [
            'label' => 'Critical',
            'value' => 5,
            'btn_class' => 'dark',
        ]);
    }
}

