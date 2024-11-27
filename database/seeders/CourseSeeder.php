<?php

namespace Database\Seeders;

use App\Models\Audience;
use App\Models\Category;
use App\Models\Course;
use App\Models\Document;
use App\Models\Goal;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\Option;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\Requirement;
use App\Models\User;
use App\Models\Video;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Truncate các bảng để làm mới dữ liệu
        DB::statement('SET FOREIGN_KEY_CHECKS=0;'); // Tắt kiểm tra khóa ngoại để truncate
        DB::table('lessons')->truncate();
        DB::table('videos')->truncate();
        // DB::table('categories')->truncate();

        DB::table('documents')->truncate();
        DB::table('modules')->truncate();
        DB::table('courses')->truncate();
        DB::table('quizzes')->truncate();
        DB::table(table: 'lesson_progress')->truncate();
        DB::table('quiz_progress')->truncate();
        DB::table('user_answers')->truncate();
        DB::table("user_courses")->truncate();
        DB::table('questions')->truncate();
        DB::table('requirements')->truncate();
        DB::table('goals')->truncate();
        DB::table('audiences')->truncate();
        DB::table('options')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;'); // Bật lại kiểm tra khóa ngoại
        //Tạo 10 giảng viên ai chưa có giảng viên thì bật cái này lên seed
        // $users = User::factory(10)->create(['user_type' => User::TYPE_TEACHER]); // Giảng viên
        // $categories = Cateogry::factory(10); // Giảng viên
        //Tính durantion video you

        //Của tôi thì lấy thằng admin thôi
        $user = User::whereIn('user_type', [User::TYPE_ADMIN, User::TYPE_TEACHER])->inRandomOrder()->first();

        // $categories = Category::factory(2)->create();

        //Tạo thời đế ít
        $youtubeIds = [
            '0SJE9dYdpps',
            '-jV06pqjUUc',
            'efI98nT8Ffo',
            'W0vEUmyvthQ',
            'CLbx37dqYEI',
            'xRpXBEq6TOY',
            'rSV33HGotgE',
            'SZb-N7TfPlw',
            'm_h7-dgKnMU',
            'aM-DUx6Qnc8',
            'ncRmjazgsE8',
            'QCLVU6cZU_E',
            'rWM2lXtS-d8',
            '9cZEG1SSSQc',
            '9MpHrdWBdxg',
            'O_-SQ-aVR3E',
            'P-fMQ3elxSE',
            'meCXeMeyFdE',
            'j1PbSq5kJKY',
            '6F_dajRCC9Q'
        ];
        //Mảng dữ liệu lấy từ public/storage/courses/thumbnails => đi vào đó và coppy 20 value của ảnh là ok
        $thumbnailCourses = [
            "course_thumbnail_0de8c851-48d8-4650-a84e-1f697f386cf4.jpg",
            "course_thumbnail_0fdc85bc-a058-4778-b124-dd1ea9efa3d4.png",
            "course_thumbnail_3a238388-702a-4ccb-a29c-872cd944d100.png",
            "course_thumbnail_3b2862dc-2bdb-45f8-ad6b-4e838eb7a4be.png",
            "course_thumbnail_3f4574ab-437c-4ef5-ae26-7ea08e42752d.jpg",
            "course_thumbnail_7cb85a6c-782d-41c1-a146-ce09726f8ea0.png",
            "course_thumbnail_7f6677cf-31cb-4e1a-ab42-e4479744294d.png",
            "course_thumbnail_51f649f9-f4f9-4239-b882-0ae9f5ac9b32.png",
            "course_thumbnail_60bc3926-67e6-4198-8da6-42f65a21c76b.png",
            "course_thumbnail_68fa1361-d514-4cc3-95f1-d32145eba7c2.png",
            "course_thumbnail_072f30fa-ba23-4609-8fcc-946ed2124d3a.png",
            "course_thumbnail_92f0137b-1b85-4ad3-bc6c-537ae2b60104.jpg",
            "course_thumbnail_683a3d0e-faf5-4127-ae95-16f726778b3d.png",
            "course_thumbnail_7655da6c-66dd-41bf-8570-6cc53c8ec9a4.png",
            "course_thumbnail_86315953-1dae-4bd3-9dab-bff438552fbc.png",
            "course_thumbnail_ab80ada4-494f-4dc7-b90c-2ff715cac5e1.png",
            "course_thumbnail_bbf0530d-5cc9-4faa-9eff-4deb52c59a24.png",
            "course_thumbnail_bdf973a7-d948-4224-8395-a680fe9d4d9c.jpg",
            "course_thumbnail_e0b65d9f-02f1-43f1-a4d7-4719b5621a6a.png",
            "course_thumbnail_e439cce0-bcc7-43ec-8f6b-22c92c0fdbc2.png"
        ];

        
        foreach (range(1, 20) as $index) {
            
            //seed khoá học
            $course = Course::create([
                'id_category' => rand(1, 2),
                'id_user' => $user->id,
                'name' => "Khoá học " . fake()->text(10),
                'thumbnail' => 'courses/thumbnails/' . $thumbnailCourses[$index - 1],
                'trailer' => 'trailers/trailer_' . $index . '.mp4',
                'description' => fake()->text('100'),
                'learned' => 'Những điều học được từ khóa học ' . $index,
                'slug' => Str::slug(fake()->name(), '-'),
                'level' => Course::LEVEL_ARRAY[array_rand(Course::LEVEL_ARRAY)],
                'duration' => Null,
                'sort_description' => fake()->text('50'),
                'price' => rand(500, 2000),
                'price_sale' => rand(100, 500),
                'total_student' => rand(0, 100),
                'is_active' => 1,
                'is_free' => rand(0, 1),
                'is_trending' => rand(0, 1),
                'status' => Course::COURSE_STATUS_APPROVED,
                'submited_at' => now(),
            ]);
            //Thêm mục tiêu, yêu cầu, hướng đối tượng nữa mỗi cái 4 cái
            foreach (range(1, 4) as $infoIndex) {
                Requirement::create([
                    'course_id' => $course->id,
                    'requirement' => fake()->text('20'),
                    'position' => $infoIndex
                ]);
                Goal::create([
                    'course_id' => $course->id,
                    'goal' => fake()->text('20'),
                    'position' => $infoIndex
                ]);
                Audience::create([
                    'course_id' => $course->id,
                    'audience' => fake()->text('20'),
                    'position' => $infoIndex
                ]);
            }



            //Tạo module cho khoá học mỗi khoá học có 5 chương
            foreach (range(1, 5) as $modIndex) {
                $module = Module::create([
                    'id_course' => $course->id,
                    'title' => 'Chương học ' . $modIndex,
                    'description' => 'Mô tả chương ' . $modIndex,
                    'position' => $modIndex,
                ]);

                // Tạo bài học (Lesson) trước
                foreach (range(1, 3) as $lessonIndex) {
                    if (rand(0, 1)) {

                        // Lấy ngẫu nhiên một video YouTube ID từ mảng
                        $youtubeId = $youtubeIds[array_rand($youtubeIds)];

                        // Lấy thời gian video từ YouTube API
                        $videoDuration = $this->getVideoDuration($youtubeId);

                        // Tạo Video và lưu thông tin vào database
                        $lessonable = Video::create([
                            'title' => 'Video ' . $lessonIndex . ' của module ' . $modIndex,
                            'type' => 'url',
                            'url' => null,
                            'video_youtube_id' => $youtubeId, // ID video YouTube
                            'duration' => $videoDuration, // Thời gian video từ API
                        ]);

                    } else {
                        $lessonable = Document::create([
                            'title' => fake()->text('20'),
                            'description' => fake()->text('10'),
                            'content' => fake()->text('200'),
                            'resourse_path' => 'documents/doc_' . $lessonIndex . '.pdf',
                        ]);
                    }

                    Lesson::create([
                        'id_module' => $module->id,
                        'title' => 'Bài học ' . $lessonIndex . ' của module ' . $modIndex,
                        'thumbnail' => 'thumbnails/lesson_' . $lessonIndex . '.jpg',
                        'description' => 'Mô tả cho bài học ' . $lessonIndex,
                        'content_type' => $lessonable instanceof Video ? 'video' : 'document',
                        'lessonable_id' => $lessonable->id,
                        'lessonable_type' => get_class($lessonable),
                        'position' => $lessonIndex, // Đảm bảo unique trong module
                    ]);
                }

                // Sau khi tạo xong tất cả các bài học thì tạo quiz
                $quiz = Quiz::create([
                    'id_module' => $module->id,
                    'title' => 'Bài tập chương ' . $modIndex,
                    'description' => 'Mô tả quiz ' . $modIndex,
                ]);

                // Tạo câu hỏi cho quiz
                foreach (range(1, 3) as $questionIndex) {
                    $question = Question::create([
                        'id_quiz' => $quiz->id,
                        'question' => fake()->text(30),
                        'type' => 'multiple_choice',
                    ]);

                    foreach (range(1, 4) as $optionIndex) {
                        Option::create([
                            'id_question' => $question->id,
                            'option' => fake()->text(20),
                            'is_correct' => $optionIndex === 1, // Đáp án đúng cố định là option 1
                        ]);
                    }
                }
            }

        }

    }
    private function convertDurationToSeconds($duration)
    {
        $interval = new \DateInterval($duration);
        return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
    }
    public function getVideoDuration($videoId)
    {
        $apiKey = env('YOUTUBE_API_KEY');

        $apiUrl = "https://www.googleapis.com/youtube/v3/videos?id={$videoId}&part=contentDetails&key={$apiKey}";

        $response = Http::get($apiUrl);

        $data = $response->json();

        if (!empty($data['items'])) {
            $duration = $data['items'][0]['contentDetails']['duration'];

            $seconds = $this->convertDurationToSeconds($duration);

            return $seconds;
        }
    }

}
