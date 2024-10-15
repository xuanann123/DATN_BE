<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->text(25),
            'description' => fake()->text(250),
            'content' => fake()->text(500),
            'thumbnail' => fake()->imageUrl(),
            'user_id' => rand(20, 50),
            'slug' => fake()->slug(),
            'status' => 'published'
        ];
    }
}
