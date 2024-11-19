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

            $course = $userCourse->course;

            $course_progress = $this->checkCourseProgress($user, $course);

            $userCourse->progress_percent = $course_progress;
            $userCourse->save();

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
            $userId = $request->user_id;
            $id_course = $request->course_id;
            $quizId = $request->quiz_id;
            $answers = $request->answers;

            // Verify if the user has purchased the course
            $userCourse = UserCourse::where('id_user', $userId)
                ->where('id_course', $id_course)
                ->first();

            if (!$userCourse) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn chưa mua khóa học này.',
                    'data' => []
                ], 403);
            }

            // Initialize counters and storage
            $correctAnswersCount = 0;
            $totalQuestions = Question::where('id_quiz', $quizId)->count();
            $resultDetails = [];

            // Collect all answers' correctness
            foreach ($answers as $answer) {
                $questionId = $answer['question_id'];
                $selectedOptions = $answer['selected_options'];
                $question = Question::find($questionId);

                if (!$question) {
                    throw new \Exception("Câu hỏi với ID {$questionId} không tồn tại.");
                }

                // Get correct options for the question
                $correctOptions = Option::where('id_question', $questionId)
                    ->where('is_correct', 1)
                    ->pluck('id')
                    ->toArray();

                // Determine if the answer is correct
                $isCorrect = false;
                if ($question->type == 'one_choice') {
                    if (count($selectedOptions) == 1 && $selectedOptions[0] == $correctOptions[0]) {
                        $isCorrect = true;
                    }
                } elseif ($question->type == 'multiple_choice') {
                    sort($selectedOptions);
                    sort($correctOptions);
                    if ($selectedOptions == $correctOptions) {
                        $isCorrect = true;
                    }
                }

                if ($isCorrect) {
                    $correctAnswersCount++;
                }

                // Store detailed result
                $resultDetails[] = [
                    'question_id' => $questionId,
                    'question_content' => $question->question,
                    'selected_options' => $selectedOptions,
                    'correct_options' => $correctOptions,
                    'is_correct' => $isCorrect,
                ];
            }

            // Calculate the percentage score
            $percentageScore = ($totalQuestions > 0)
                ? ($correctAnswersCount / $totalQuestions) * 100
                : 0;

            // Prepare the response data
            $data = [
                'user_id' => $userId,
                'quiz_id' => $quizId,
                'total_score' => $percentageScore,
                'result_details' => $resultDetails
            ];

            // Check if all answers are correct
            if ($correctAnswersCount === $totalQuestions) {
                // Save user answers
                foreach ($answers as $answer) {
                    $questionId = $answer['question_id'];
                    $selectedOptions = $answer['selected_options'];

                    foreach ($selectedOptions as $optionId) {
                        UserAnswer::updateOrCreate(
                            [
                                'user_id' => $userId,
                                'quiz_id' => $quizId,
                                'question_id' => $questionId,
                                'option_id' => $optionId
                            ],
                            [
                                'user_id' => $userId,
                                'quiz_id' => $quizId,
                                'question_id' => $questionId,
                                'option_id' => $optionId,
                            ]
                        );
                    }
                }

                return response()->json([
                    'status' => 'success',
                    'message' => 'Chúc mừng! Bạn đã trả lời đúng tất cả các câu hỏi.',
                    'data' => $data
                ], 200);
            } else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Vui lòng thực hiện lại bài tập.',
                    'data' => $data
                ], 200);
            }

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
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
                'score' => $request->score,
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

    public function getQuizResult(Request $request, string $userId, string $quizId)
    {
        try {
            // Truy vấn các câu trả lời của người dùng từ bảng user_answers
            $userAnswers = UserAnswer::where('user_id', $userId)
                ->where('quiz_id', $quizId)
                ->with(['question', 'option']) // Lấy thông tin câu hỏi và đáp án
                ->get();

            // Kiểm tra nếu không có câu trả lời nào trong quiz
            if ($userAnswers->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không có kết quả cho bài quiz này.',
                    'data' => []
                ], 404);
            }


            // Nhóm các câu trả lời cùng question_id lại với nhau
            $groupedAnswers = $userAnswers->groupBy('question_id');


            // Chuẩn bị dữ liệu kết quả
            $result = [];
            foreach ($groupedAnswers as $questionId => $answers) {
                $selectedOptions = $answers->pluck('option.id')->all(); // Lấy tất cả option_id cho question_id này

                $result[] = [
                    'question_id' => $questionId,
                    'selected_option_id' => $selectedOptions // Mảng các selected_option_id
                ];
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả bài quiz',
                'data' => [
                    'user_id' => $userId,
                    'quiz_id' => $quizId,
                    'answers' => $result, // Chi tiết câu trả lời đã nhóm
                ]
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ]);
        }
    }

    private function checkCourseProgress($user ,$course)
    {
        // Tống số lượng bài học trong khoá học
        $total_lessons = $course->modules->flatMap->lessons->count();
        // Tổng số lượng quiz trong khóa học
        $total_quizzes = $course->modules->whereNotNull('quiz')->count();
        // Tổng bài học + quiz
        $total_items = $total_lessons + $total_quizzes;

        // Tổng số lượng bài học đã học
        $completed_lessons = LessonProgress::where('id_user', $user->id)
            ->where('is_completed', 1)
            ->whereIn('id_lesson', $course->modules->flatMap->lessons->pluck('id'))
            ->count();

        // Số lượng quiz đã hoàn thành
        $completed_quizzes = QuizProgress::where('user_id', $user->id)
            ->where('is_completed', 1)
            ->whereIn('quiz_id', $course->modules->pluck('quiz.id'))
            ->count();

        // Tổng số lượng bài học và quiz đã hoàn thành
        $total_completed_items = $completed_lessons + $completed_quizzes;

        // Tính tiến độ khoá học người dùng đã đăng kí khoá học sẽ là (tổng bài đã hoàn thiện)/ (tổng bài học) * 100 = tiến độ (%)
        $progress_percent = $total_items > 0 ? ($total_completed_items / $total_items) * 100 : 0;

        return $progress_percent;
    }

}
