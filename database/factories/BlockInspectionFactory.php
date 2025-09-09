<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockInspection>
 */
class BlockInspectionFactory extends Factory
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
            'ref_no' => $this->faker->unique()->regexify('INSP[0-9]{6}[0-9]{4}'),
            'scheduled_date_time' => $this->faker->dateTimeBetween('now', '+30 days'),
            'start_date_time' => null,
            'end_date_time' => null,
            'notes' => $this->faker->sentence(10),
            'pdf_path' => null,
            'pdf_name' => null,
            'job_status_id' => $this->faker->numberBetween(1, 5), // 1=Scheduled, 2=In Progress, 3=Completed, 4=Cancelled, 5=On Hold
            'is_mobile' => $this->faker->boolean(20), // 20% chance of being mobile
            'created_by' => User::factory(),
            'updated_by' => null,
            'deleted_by' => null,
        ];
    }

    /**
     * Indicate that the inspection is scheduled.
     */
    public function scheduled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'job_status_id' => 1,
                'start_date_time' => null,
                'end_date_time' => null,
            ];
        });
    }

    /**
     * Indicate that the inspection is in progress.
     */
    public function inProgress(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'job_status_id' => 2,
                'start_date_time' => $this->faker->dateTimeBetween('-1 day', 'now'),
                'end_date_time' => null,
            ];
        });
    }

    /**
     * Indicate that the inspection is completed.
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $startTime = $this->faker->dateTimeBetween('-2 days', '-1 day');
            return [
                'job_status_id' => 3,
                'start_date_time' => $startTime,
                'end_date_time' => $this->faker->dateTimeBetween($startTime, 'now'),
            ];
        });
    }

    /**
     * Indicate that the inspection is cancelled.
     */
    public function cancelled(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'job_status_id' => 4,
                'start_date_time' => null,
                'end_date_time' => null,
            ];
        });
    }

    /**
     * Indicate that the inspection is on hold.
     */
    public function onHold(): static
    {
        return $this->state(function (array $attributes) {
            return [
                'job_status_id' => 5,
                'start_date_time' => null,
                'end_date_time' => null,
            ];
        });
    }
}
