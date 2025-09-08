<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\UserType>
 */
class UserTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true),
            'description' => fake()->sentence(),
            'is_hidden' => false,
        ];
    }

    /**
     * Create a hidden user type.
     */
    public function hidden(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hidden' => true,
        ]);
    }

    /**
     * Create a visible user type.
     */
    public function visible(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hidden' => false,
        ]);
    }

    /**
     * Create a contractor admin user type.
     */
    public function contractorAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Contractor Admin',
            'description' => 'Contractor Administrator with management access over contractor users',
            'is_hidden' => false,
        ]);
    }

    /**
     * Create a contractor user type.
     */
    public function contractorUser(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Contractor User',
            'description' => 'Contractor User with limited access for assigned tasks',
            'is_hidden' => false,
        ]);
    }

    /**
     * Create an admin user type.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Admin',
            'description' => 'System Administrator with full access',
            'is_hidden' => false,
        ]);
    }

    /**
     * Create a financial admin user type (hidden).
     */
    public function financialAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'name' => 'Financial Admin',
            'description' => 'Financial Administrator with financial management access',
            'is_hidden' => true,
        ]);
    }
}
