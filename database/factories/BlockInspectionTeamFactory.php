<?php

namespace Database\Factories;

use App\Models\BlockInspection;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockInspectionTeam>
 */
class BlockInspectionTeamFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'block_inspection_id' => BlockInspection::factory(),
            'user_id' => User::factory(),
            'role' => $this->faker->randomElement(['Lead Inspector', 'Inspector', 'Assistant Inspector']),
            'is_lead' => false,
        ];
    }

    /**
     * Indicate that this team member is the lead inspector.
     */
    public function lead(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'Lead Inspector',
                'is_lead' => true,
            ];
        });
    }

    /**
     * Indicate that this team member is a regular inspector.
     */
    public function inspector(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'Inspector',
                'is_lead' => false,
            ];
        });
    }

    /**
     * Indicate that this team member is an assistant inspector.
     */
    public function assistant(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'role' => 'Assistant Inspector',
                'is_lead' => false,
            ];
        });
    }
}
