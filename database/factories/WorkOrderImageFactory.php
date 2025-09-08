<?php

namespace Database\Factories;

use App\Models\WorkOrder;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\WorkOrderImage>
 */
class WorkOrderImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'work_order_id' => WorkOrder::factory(),
            'image' => $this->faker->uuid() . '.jpg',
            'common_status_id' => 1,
        ];
    }

    /**
     * Create an image with specific status.
     */
    public function withStatus($statusId): static
    {
        return $this->state(fn (array $attributes) => [
            'common_status_id' => $statusId,
        ]);
    }
}
