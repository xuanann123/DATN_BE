<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Lesson::all()->each(function ($course) {
            for ($i = 0; $i < 50; $i++) {
                $course->comments()->create([
                    'id_user' => rand(1, 90),
                    'content' => fake()->text('25')
                ]);
            }
        });
    }
}
