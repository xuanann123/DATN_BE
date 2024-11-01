<?php

namespace App\Http\Controllers\api\Client\Student;

use App\Models\Quiz;
use App\Models\Video;
use App\Models\Lesson;
use App\Models\Document;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use App\Models\LessonProgress;
use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Lessons\LessonProgressRequest;
use App\Http\Requests\Client\Lessons\QuizProgressRequest;
use App\Models\Option;
use App\Models\Question;
use App\Models\QuizProgress;
use App\Models\UserAnswer;

class LessonController extends Controller
{
    //Xử lý authentication => mí xem được chi tiết bài học
    public function lessonDetail(Lesson $lesson)
    {
        try {
            //Lấy người dùng hiện tại
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();
            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }
            //Lấy bài học đó ra
            $lesson = Lesson::with(['lessonable'])
                ->where('id', $lesson->id)
                ->firstOrFail();
            //Nếu tồn tại bài học đó thì trả về dữ liệu như bên
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin chi tiết bài học.",
                'data' => $lesson,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server báo lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Chi tiết quiz
    public function quizDetail(Quiz $quiz)
    {
        try {
            //Lấy người dùng hiện tại
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $quiz->module->id_course)
                ->first();
            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }
            //Lấy quiz đó ra
            $quiz = Quiz::with(['questions.options'])
                ->where('id', $quiz->id)
                ->firstOrFail();
            //Nếu tồn tại quiz đó thì trả về dữ liệu như bên
            return response()->json([
                'status' => 'success',
                'message' => "Thông tin chi tiết bài học.",
                'data' => $quiz,
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server báo lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi lấy thông tin bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // API cập nhật tiến độ bài học
    public function updateLessonProgress(LessonProgressRequest $request, Lesson $lesson)
    {
        try {
            // Lấy người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $lesson->module->id_course)
                ->first();

            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Xác định loại bài học
            $data = [
                'id_user' => $user->id,
                'id_lesson' => $lesson->id,
                'is_completed' => $request->is_completed,
            ];

            // Nếu bài học là vid, cập nhật last_time_video
            if ($lesson->lessonable_type === Video::class && $request->has('last_time_video')) {
                $data['last_time_video'] = $request->last_time_video;
            }

            // Cập nhật hoặc tạo mới tiến độ bài học
            $lessonProgress = LessonProgress::updateOrCreate(
                //Check điều kiện này đã tồn tại rồi thì đi update => còn không thì đi cập nhật
                [
                    'id_user' => $user->id,
                    'id_lesson' => $lesson->id,
                ],
                $data
            );

            // response cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Tiến độ bài học đã được cập nhật.',
                'data' => $lessonProgress,
            ], 200);

        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật tiến độ bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function checkQuiz(Request $request)
    {

        try {
            // Lấy người dùng đang đăng nhập
            $id_user = $request->user_id;
            //Lấy id khoá học từ dưới client
            $id_course = $request->course_id;
            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $id_user)
                ->where('id_course', $id_course)
                ->first();
            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }
            //Lưu thông tin thằng nào làm bài
            $userId = $request->user_id;
            //Truy vấn xem lấy id quiz của khoá học ra
            $quizId = $request->quiz_id;
            //Và câu trả lời của thằng làm bài đó
            $answers = $request->answers;
            //Tôi muốn tính tổng điểm sẽ là số % câu trả lời đúng trên toàn bộ câu trả lời
            $correctAnswersCount = 0;
            //Tổng số lượng câu hỏi
            $totalQuestions = Question::where('id_quiz', $quizId)->count();
            //Duyệt qua mảng dữ liệu mảng câu trả lời
            foreach ($answers as $answer) {
                //Tìm xem câu hỏi đó là câu nào
                $questionId = $answer['question_id'];
                //Câu trả lời của nó gồm những thằng nào
                $selectedOptions = $answer['selected_options'];

                // Lưu trữ câu trả lời vào bảng user_answers
                foreach ($selectedOptions as $optionId) {
                    UserAnswer::create([
                        'user_id' => $userId,
                        'quiz_id' => $quizId,
                        'question_id' => $questionId,
                        'option_id' => $optionId,
                    ]);
                }

                //Lấy thông tin của câu hỏi và đáp án đúng là câu nào


                $question = Question::find($questionId);
                //Kiểm tra xem câu trả lời nào đúng
                $correctOptions = Option::where('id_question', $questionId)
                    ->where('is_correct', 1)
                    ->pluck('id')
                    ->toArray();

                if ($question->type == 'one_choice') {
                    // Với câu hỏi chọn một đáp án đúng
                    if (count($selectedOptions) == 1 && $selectedOptions[0] == $correctOptions[0]) {
                        $correctAnswersCount++;
                    }
                } elseif ($question->type == 'multiple_choice') {
                    // Với câu hỏi chọn nhiều đáp án đúng, cần kiểm tra tất cả đáp án người dùng
                    sort($selectedOptions);
                    sort($correctOptions);
                    if ($selectedOptions == $correctOptions) {
                        $correctAnswersCount++;
                    }
                }
            }
            $percentageScore = ($totalQuestions > 0)
                ? ($correctAnswersCount / $totalQuestions) * 100
                : 0;
            $data = [
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'total_score' => $percentageScore
            ];
            if ($percentageScore == 100) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Bạn đã hoàn thành bài tập',
                    'data' => $data
                ], 200);
            }

            return response()->json([
                'status' => 'error',
                'message' => 'Vui lòng thực hiện lại bài tập',
                'data' => $data
            ], status: 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }

    }

    //Cập nhật dữ liệu khi làm xong bài tập quiz
    public function updateQuizProgress(QuizProgressRequest $request, Quiz $quiz)
    {
        try {
            // Lấy người dùng đang đăng nhập
            $user = auth()->user();

            // Kiểm tra người dùng đã mua khoá học đó chưa
            $userCourse = UserCourse::where('id_user', $user->id)
                ->where('id_course', $quiz->module->id_course)
                ->first();

            //Nếu chưa mua thì báo lỗi 403 cấm truy cập
            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Xác định loại bài học
            $data = [
                'user_id' => $user->id,
                'quiz_id' => $quiz->id,
                'is_completed' => $request->is_completed,
            ];

            // Cập nhật hoặc tạo mới tiến độ bài học
            $quizProgress = QuizProgress::updateOrCreate(
                //Check điều kiện này đã tồn tại rồi thì đi update => còn không thì đi cập nhật
                [
                    'user_id' => $user->id,
                    'quiz_id' => $quiz->id,
                ],
                $data
            );

            // response cho client
            return response()->json([
                'status' => 'success',
                'message' => 'Tiến độ bài học đã được cập nhật.',
                'data' => $quizProgress,
            ], 200);

        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi cập nhật tiến độ bài học.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
