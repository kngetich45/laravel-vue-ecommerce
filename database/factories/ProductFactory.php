<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(4, true), // Generates a product name like "Smart Watch"
            'description' => fake()->sentence(15), // Generates a product description
            'price' => fake()->randomFloat(2, 10, 1000), // Price between 10 and 1000
            'image_url' => fake()->imageUrl(640, 480, 'products', true, 'Shopping'), // Product image URL
        ];
    }
}
