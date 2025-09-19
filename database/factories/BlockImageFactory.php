<?php

namespace Database\Factories;

use App\Models\Block;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlockImage>
 */
class BlockImageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $fileName = $this->faker->uuid() . '.jpg';
        
        return [
            'block_id' => Block::factory(),
            'original_name' => $this->faker->word() . '.jpg',
            'stored_name' => $fileName,
            'file_path' => 'blocks/1', // Default path
            'file_extension' => 'jpg',
            'file_size' => $this->faker->numberBetween(100000, 5000000), // 100KB to 5MB
            'mime_type' => 'image/jpeg',
            'sort_order' => $this->faker->numberBetween(0, 10),
            'is_primary' => false,
            'uploaded_by' => User::factory(),
        ];
    }

    /**
     * Indicate that the image is primary.
     */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }

    /**
     * Indicate that the image is a PNG.
     */
    public function png(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_extension' => 'png',
            'mime_type' => 'image/png',
            'original_name' => $this->faker->word() . '.png',
            'stored_name' => $this->faker->uuid() . '.png',
        ]);
    }

    /**
     * Indicate that the image is a GIF.
     */
    public function gif(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_extension' => 'gif',
            'mime_type' => 'image/gif',
            'original_name' => $this->faker->word() . '.gif',
            'stored_name' => $this->faker->uuid() . '.gif',
        ]);
    }

    /**
     * Create a small file size image.
     */
    public function small(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_size' => $this->faker->numberBetween(10000, 500000), // 10KB to 500KB
        ]);
    }

    /**
     * Create a large file size image.
     */
    public function large(): static
    {
        return $this->state(fn (array $attributes) => [
            'file_size' => $this->faker->numberBetween(3000000, 5000000), // 3MB to 5MB
        ]);
    }

    /**
     * Create image with specific sort order.
     */
    public function sortOrder(int $order): static
    {
        return $this->state(fn (array $attributes) => [
            'sort_order' => $order,
        ]);
    }
}
