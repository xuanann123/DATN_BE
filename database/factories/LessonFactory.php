<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Lesson>
 */
class LessonFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Seeder lesson video
//        return [
//            'id_module' => rand(100, 1580),
//            'title' => fake()->text(25),
//            'description' => fake()->text(200),
//            'content_type' => 'video',
//            'lessonable_type' => 'App\Models\Video',
//            'lessonable_id' => rand(1, 500),
//            'is_active' => 1,
//        ];
//
        // Seeder lesson document
        return [
            'id_module' => rand(100, 1580),
            'title' => fake()->text(25),
            'description' => fake()->text(200),
            'content_type' => 'document',
            'lessonable_type' => 'App\Models\Document',
            'lessonable_id' => rand(1, 100),
            'is_active' => 1,
        ];
    }
}
