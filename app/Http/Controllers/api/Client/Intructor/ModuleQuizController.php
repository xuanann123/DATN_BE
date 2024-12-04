<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\Quiz;
use App\Models\Module;
use App\Models\Option;
use App\Models\Question;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Client\Quiz\StoreQuizRequest;
use App\Http\Requests\Client\Quiz\UpdateQuizRequest;
use Illuminate\Support\Facades\Http;

class ModuleQuizController extends Controller
{
    # ============================== Controller with Quiz ============================== #
    //Chỉ thêm được 1 quiz cho 1 chương học
    public function addQuiz(StoreQuizRequest $request, Module $module)
    {

        if (!$module) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy chương học',
                'data' => []
            ], 404);
        }
        try {
            $quiz = Quiz::create([
                'id_module' => $module->id,
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ]);
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm bài tập thành công',
                'data' => $quiz
            ], 201);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
    //Sửa quiz
    public function updateQuiz(UpdateQuizRequest $request, Quiz $quiz)
    {

        try {
            //Nếu không tồn tại quiz
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy bài tập',
                    'data' => []
                ], 404);
            }
            //Update quiz khi lấy được dữ liệu từ ô input
            $quiz->update([
                'title' => $request->input('title'),
                'description' => $request->input('description'),
            ]);
            //Trả về dữ liệu khi update thành công
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật bài tập thành công',
                'data' => $quiz
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }

    }
    //Xoá quiz
    public function deleteQuiz(Quiz $quiz)
    {
        try {
            //Kiểm tra quiz tồn tại trước khi xoá không
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tìm thấy quiz',
                    'data' => []
                ], 404);
            }
            //Xoá quiz
            $quiz->delete();
            //Trả dữ liệu delete quiz success
            return response()->json([
                'status' => 'success',
                'message' => 'Xoá bài tập thành công',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    #=================================== Controller with Questions ===============================
    //Danh sách quiz theo module
    public function showQuiz(string $id)
    {
        //Dữ liệu theo tên chương tên khoá học + người tạo khoá học
        $module = Module::with(['quiz', 'course.user'])->where('id', $id)->first();
        //Tiếp theo lấy quiz của nó để truy vấn sâu để đổ dữ liệu ra
        $quiz = Quiz::with(['questions.options', 'module.course'])->where('id_module', $id)->first();
        if (!$module) {
            return response()->json([
                'status' => 'error',
                'message' => 'Không tìm thấy bài tập',
                'data' => []
            ]);
        }
        //Đổ câu hỏi ra
        return response()->json([
            'status' => 'success',
            'message' => 'Lấy bài tập thành công',
            'data' => [
                // 'module' => $module,
                'quiz' => $quiz
            ]
        ]);
    }
    //Thêm dữ liệu question và options cho Quiz
    public function addQuestionAndOption(Request $request, Quiz $quiz)
    {
        DB::beginTransaction();
        try {
            //Nếu không tồn tại quiz
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bài tập không tồn tại',
                    'data' => []
                ], 404);
            }

            $questionData = $request->question;
            // Kiểm tra nếu không có 'correct_answer' trong câu hỏi, trả về lỗi
            if (!isset($questionData['correct_answer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phải chọn đáp án đúng cho câu hỏi.',
                    'data' => []
                ], 400);
            }
            // // Thêm dữ liệu bằng cách như sau
            $quizQuestion = Question::create([
                'id_quiz' => $quiz->id,  // Thêm quiz_id nếu cần
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'image_url' => $this->uploadImage($questionData['image'] ?? NULL, 'questions')
            ]);
            // Tạo 1 vòng duyệt qua từng option rồi thêm dữ liệu vào database
            foreach ($request->input('options') as $optionIndex => $optionData) {
                $optionText = is_array($optionData) ? ($optionData['text'] ?? null) : $optionData;
                $optionImage = $request->file("options.{$optionIndex}.image") ?? null;
                // nếu câu trả lời không có text hoặc image thì chặn
                if (empty($optionText) && !$optionImage) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Phải có nội dung cho câu trả lời thứ ' . ($optionIndex + 1),
                        'data' => []
                    ], 400);
                }
                $option = Option::create([
                    'id_question' => $quizQuestion->id,
                    'option' => $optionText,
                    'image_url' => $this->uploadImage($optionImage, 'options'),
                    'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex)
                ]);
            }

            Db::commit();

            // Lấy câu hỏi vừa tạo cùng với các options
            $questionWithOptions = Question::with('options')->find($quizQuestion->id);

            // Trả về dữ liệu khi update này
            return response()->json([
                'status' => 'success',
                'message' => 'Thêm bài tập thành công',
                'data' => [
                    'quiz' => $quiz,
                    'question' => $questionWithOptions
                ],
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            //Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function showQuestionAndOption(Question $question)
    {
        try {
            // Nếu không tồn tại câu hỏi
            if (!$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Câu hỏi không tồn tại',
                    'data' => []
                ], 404);
            }

            // Lấy câu hỏi cùng với các options
            $questionWithOptions = Question::with('options')->find($question->id);

            // Trả về response khi thành công
            return response()->json([
                'status' => 'success',
                'message' => 'Thông tin câu hỏi và câu trả lời',
                'data' => $questionWithOptions
            ], 200);
        } catch (\Exception $e) {
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function updateQuestionAndOption(Request $request, Question $question)
    {
        DB::beginTransaction();
        try {
            // nếu không tồn tại câu hỏi
            if (!$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tồn tại câu hỏi',
                    'data' => []
                ], 404);
            }
            //
            $questionData = $request->question;
            // Kiểm tra nếu không có 'correct_answer' trong câu hỏi, trả về lỗi
            if (!isset($questionData['correct_answer'])) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Phải chọn đáp án đúng cho câu hỏi.',
                    'data' => []
                ], 400);
            }

            $questionImage = $request->file('question.image') ?? null;

            // Xử lý việc xóa ảnh của question nếu có yêu cầu 'remove_image' gửi từ FE
            if (!isset($questionData['image'])) {
                if ($question->image_url) {
                    Storage::delete($question->image_url); // del ảnh trong storage
                }
                $question->image_url = null; // Xóa đường dẫn image trong db
                // Kiểm tra nếu có ảnh mới upload
            } elseif ($questionImage) {
                $question->image_url = $this->uploadImage($questionImage, 'questions', $question->image_url);
            }

            // update question
            $question->update([
                'question' => $questionData['question'],
                'type' => $questionData['type'],
                'image_url' => $question->image_url,
            ]);

            // Lấy danh sách id các options từ request
            $requestOptionIds = collect($request->options)->pluck('id')->filter()->all();

            // Xóa các options không có trong request
            $question->options()->whereNotIn('id', $requestOptionIds)->each(function ($option) {
                if ($option->image_url) {
                    Storage::delete($option->image_url);
                }
                $option->delete(); // Xoa option
            });

            // update or create options
            foreach ($request->options as $optionIndex => $optionData) {
                $optionId = $optionData['id'] ?? NULL; // check xem co id cua option khong ?
                $optionText = is_array($optionData) ? ($optionData['text'] ?? null) : $optionData;
                $optionImage = $request->file("options.{$optionIndex}.image") ?? null;

                if ($optionId) {
                    // update option nếu có optionId
                    $option = Option::find($optionId);
                    if ($option) {
                        $imageWasRemoved = false;
                        // Xử lý việc xóa ảnh của option nếu có yêu cầu 'remove_image' gửi từ FE
                        if (isset($optionData['remove_image']) && $optionData['remove_image'] == true) {
                            // đánh dấu sẽ xóa ảnh
                            $imageWasRemoved = true;
                            $oldImageUrl = $option->image_url; // duong anh can xoa
                            $option->image_url = null; // Xóa đường dẫn img trong db
                        } elseif ($optionImage) {
                            // Kiểm tra nếu có ảnh mới upload
                            $option->image_url = $this->uploadImage($optionImage, 'options', $option->image_url);
                        }

                        // nếu câu trả lời không có text hoặc image thì chặn
                        if (empty($optionText) && (!$option->image_url) && !$optionImage) {
                            return response()->json([
                                'status' => 'error',
                                'message' => 'Phải có nội dung cho câu trả lời thứ ' . ($optionIndex + 1),
                                'data' => []
                            ], 400);
                        }

                        if ($imageWasRemoved && $option->image_url === null) {
                            Storage::delete($oldImageUrl); // del ảnh trong storage
                        }

                        $option->update([
                            'option' => $optionText,
                            'image_url' => $option->image_url,
                            'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex),
                        ]);
                    }
                } else {
                    // nếu câu trả lời không có text hoặc image thì chặn
                    if (empty($optionText) && !$optionImage) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Phải có nội dung cho câu trả lời thứ ' . ($optionIndex + 1),
                            'data' => []
                        ], 400);
                    }
                    // create option nếu không có optionId
                    Option::create([
                        'id_question' => $question->id,
                        'option' => $optionText,
                        'image_url' => $this->uploadImage($optionImage, 'options'),
                        'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex)
                    ]);
                }
            }

            DB::commit();

            // Lấy question cùng options vừa update
            $updatedQuestion = Question::with('options')->find($question->id);

            // Trả về dữ liệu khi update này
            return response()->json([
                'status' => 'success',
                'message' => 'Cập nhật thành công câu hỏi và lựa chọn.',
                'data' => $updatedQuestion,
                // 'test' => $questionImage
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . $e->getLine(),
                'data' => []
            ], 500);
        }
    }

    public function importQuestionsAndOptions(Request $request, Quiz $quiz)
    {
        DB::beginTransaction();
        $importedImages = []; // Mảng ảnh
        $newQuestions = []; // Mảng câu hỏi mới

        try {
            if (!$quiz) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bài tập không tồn tại',
                    'data' => []
                ], 404);
            }

            $questionsData = $request->input('questions');

            foreach ($questionsData as $questionIndex => $questionData) {
                // return response()->json([
                //     'test' => $questionData,
                // ]);
                // Kiểm tra nếu không có 'correct_answer' trong câu hỏi, trả về lỗi
                if (!isset($questionData['correct_answer'])) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Phải chọn đáp án đúng cho câu hỏi.',
                        'data' => []
                    ], 400);
                }
                // Tải ảnh câu hỏi từ url
                $questionImage = $this->downloadImageFromUrl($questionData['image'] ?? null, 'questions', $importedImages);

                $newQuestion = Question::create([
                    'id_quiz' => $quiz->id,
                    'question' => $questionData['question'],
                    'type' => $questionData['type'],
                    'image_url' => $questionImage,
                ]);

                // Lưu câu hỏi vào mảng để lấy sau
                $newQuestions[] = $newQuestion;


                // Them dap an
                foreach ($questionData['options'] as $optionIndex => $optionData) {
                    $optionText = is_array($optionData) ? ($optionData['text'] ?? null) : $optionData;
                    // Tải ảnh option từ url
                    $optionImage = $this->downloadImageFromUrl($optionData['image'] ?? null, 'options', $importedImages);

                    // nếu câu trả lời không có text hoặc image thì chặn
                    if (empty($optionText) && !$optionImage) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'Phải có nội dung cho câu trả lời thứ ' . ($optionIndex + 1),
                            'data' => []
                        ], 400);
                    }

                    $option = Option::create([
                        'id_question' => $newQuestion->id,
                        'option' => $optionText,
                        'image_url' => $optionImage,
                        'is_correct' => $this->isCorrectAnswer($questionData, $optionIndex)
                    ]);
                }
            }

            DB::commit();

            // Lấy câu hỏi vừa tạo cùng với các options
            $questionWithOptions = Question::with('options')->whereIn('id', collect($newQuestions)->pluck('id'))->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Nhập câu hỏi và câu trả lời thành công.',
                'data' => [
                    'quiz' => $quiz,
                    'question' => $questionWithOptions,
                    // 'test_image' => $importedImages,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            // Xóa các ảnh đã tải về nếu có lỗi
            foreach ($importedImages as $filePath) {
                Storage::delete($filePath);
            }
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage() . $e->getLine(),
                'data' => []
            ], 500);
        }
    }

    public function deleteQuestionAndOption(Question $question)
    {
        try {
            // nếu không tồn tại câu hỏi
            if (!$question) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không tồn tại câu hỏi',
                    'data' => []
                ], 404);
            }

            // Xóa ảnh của question nếu có
            if ($question->image_url) {
                Storage::delete($question->image_url);
            }

            // Lấy tất cả các options của question
            $options = $question->options;

            // Xoá từng option của question
            foreach ($options as $option) {
                // Xoá ảnh của option nếu có
                if ($option->image_url) {
                    Storage::delete($option->image_url);
                }
                $option->delete();
            }

            // Del question
            $question->delete();

            // Trả về response khi xóa thành công
            return response()->json([
                'status' => 'success',
                'message' => 'Xóa câu hỏi và các lựa chọn thành công.',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            // Lỗi server
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
    //Kiểm tra đáp án đúng không

    private function isCorrectAnswer($question, $optionIndex)
    {
        if ($question['type'] === 'one_choice') {
            // Với one_choice, correct_answer là một giá trị số (index)
            return $optionIndex == $question['correct_answer'];
        } elseif ($question['type'] === 'multiple_choice') {
            // Với multiple_choice, correct_answer là một mảng chứa các index
            return in_array($optionIndex, $question['correct_answer']);
        }
        return false;
    }

    private function uploadImage($image, $type, $currentImage = null)
    {
        if ($image && $image->isValid()) {
            // Xóa ảnh cũ nếu tồn tại
            if ($currentImage) {
                Storage::delete($currentImage);
            }
            $newNameImage = $type . '_' . Str::uuid() . '.' . $image->getClientOriginalExtension();
            return Storage::putFileAs('images/' . $type, $image, $newNameImage);
            // return $newNameImage;
        }
        return null;
    }

    private function downloadImageFromUrl($url, $type, &$importedImages)
    {
        if ($url) {
            $imageData = @file_get_contents($url);

            if ($imageData === false) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Không thể tải ảnh từ URL: ' . $url,
                    'data' => []
                ], 400);
            }

            // Lấy định dạng từ url
            $extention = pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION);

            $imageName = $type . '_' . Str::uuid() . '.' . $extention;
            $filePath = 'images/' . $type . '/' . $imageName;

            // save
            Storage::put($filePath, $imageData);
            $importedImages[] = $filePath; // them anh vao mang (dung cho rollback)

            return $filePath;
        }
        return null;
    }

}
