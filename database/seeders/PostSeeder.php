<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    //truncate table posts

    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Tắt kiểm tra khóa ngoại để truncate
        DB::table('posts')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Bật lại kiểm tra khóa ngoại
        //Mảng dữ liệu ảnh về posts
        $arrayThumbnailPosts = [
            "posts/course_thumbnail_0de8c851-48d8-4650-a84e-1f697f386cf4.jpg",
            "posts/course_thumbnail_0fdc85bc-a058-4778-b124-dd1ea9efa3d4.png",
            "posts/course_thumbnail_3a238388-702a-4ccb-a29c-872cd944d100.png",
            "posts/course_thumbnail_3b2862dc-2bdb-45f8-ad6b-4e838eb7a4be.png",
            "posts/course_thumbnail_3f4574ab-437c-4ef5-ae26-7ea08e42752d.jpg",
            "posts/course_thumbnail_7cb85a6c-782d-41c1-a146-ce09726f8ea0.png",
            "posts/course_thumbnail_7f6677cf-31cb-4e1a-ab42-e4479744294d.png",
            "posts/course_thumbnail_51f649f9-f4f9-4239-b882-0ae9f5ac9b32.png",
            "posts/course_thumbnail_60bc3926-67e6-4198-8da6-42f65a21c76b.png",
            "posts/course_thumbnail_68fa1361-d514-4cc3-95f1-d32145eba7c2.png",
            "posts/course_thumbnail_072f30fa-ba23-4609-8fcc-946ed2124d3a.png",
            "posts/course_thumbnail_92f0137b-1b85-4ad3-bc6c-537ae2b60104.jpg",
            "posts/course_thumbnail_683a3d0e-faf5-4127-ae95-16f726778b3d.png",
            "posts/course_thumbnail_7655da6c-66dd-41bf-8570-6cc53c8ec9a4.png",
            "posts/course_thumbnail_86315953-1dae-4bd3-9dab-bff438552fbc.png",
            "posts/course_thumbnail_ab80ada4-494f-4dc7-b90c-2ff715cac5e1.png",
            "posts/course_thumbnail_bbf0530d-5cc9-4faa-9eff-4deb52c59a24.png",
            "posts/course_thumbnail_bdf973a7-d948-4224-8395-a680fe9d4d9c.jpg",
            "posts/course_thumbnail_e0b65d9f-02f1-43f1-a4d7-4719b5621a6a.png",
            "posts/course_thumbnail_e439cce0-bcc7-43ec-8f6b-22c92c0fdbc2.png"
        ];
        $userIds = User::pluck('id')->all();
        $tagIds = Tag::pluck('id')->all();
        $categoryIds = Category::pluck('id')->all();

        for ($i = 1; $i <= 20; $i++) {
            $post = Post::create([
                'title' => 'Bài viết số ' . $i,
                'description' => 'Mô tả cho bài viết số ' . $i,
                'content' => 'Nội dung chi tiết của bài viết số ' . $i,
                'thumbnail' => $arrayThumbnailPosts[array_rand($arrayThumbnailPosts)],
                'user_id' => $userIds[array_rand($userIds)], // Gán user ngẫu nhiên
                'slug' => Str::slug('Bài viết số ' . $i) . '-' . Str::random(5),
                'status' => 'published',
                'views' => rand(0, 1000),
                'allow_comments' => (bool) rand(0, 1),
                'published_at' => now()->subDays(rand(0, 30)),
            ]);

            if (!empty($tagIds)) {
                $selectedTagIds = collect($tagIds)->random(rand(1, min(3, count($tagIds)))); // Chọn từ 1 đến 3 tags
                $post->tags()->attach($selectedTagIds);
            }

            if (!empty($categoryIds)) {
                $selectedCategoryIds = collect($categoryIds)->random(rand(1, min(2, count($categoryIds)))); // Chọn từ 1 đến 2 categories
                $post->categories()->attach($selectedCategoryIds);
            }
        }
    }
}
