<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Course>
 */
class CourseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_user' => rand(1, 22),
            'id_category' => rand(1, 6),
            'name' => fake()->text('20'),
            'slug' => fake()->text('20'),
            'thumbnail' => fake()->imageUrl(),
            'price' => rand(1000, 5000),
            'is_free' => 1,
            'status' => 'approved',
            'is_active' => 1,
        ];
    }
}
