<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\Post;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
//        $post = Post::find(50);
//            for ($i = 0; $i < 150; $i++) {
//                $post->comments()->create([
//                    'id_user' => rand(20, 90),
//                    'content' => fake()->text('25'),
//                    // 'parent_id' => NULL,
//                    // 'parent_id' => rand(1, 50),
//                ]);
//            }

        $course = Course::find(6);
        for ($i = 0; $i < 50; $i++) {
            $course->comments()->create([
                'id_user' => rand(20, 90),
                'content' => fake()->text('25'),
                // 'parent_id' => NULL,
                // 'parent_id' => rand(1, 50),
            ]);
        }
    }
}
