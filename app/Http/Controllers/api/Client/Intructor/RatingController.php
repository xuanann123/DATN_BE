<?php

namespace App\Http\Controllers\api\Client\Intructor;

use App\Models\User;
use App\Models\Rating;
use App\Models\RatingReply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function replyToRating(Request $request, Rating $rating)
    {
        try {
            DB::beginTransaction();
            // Kiểm tra xem người dùng có phải là giảng viên không
            $user = Auth::user();
            if ($user->user_type !== User::TYPE_TEACHER && $user->user_type !== User::TYPE_ADMIN) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Bạn không có quyền trả lời đánh giá.'
                ], 403);
            }

            // // Kiểm tra xem đánh giá có tồn tại không
            // $rating = Rating::find($ratingId);
            // if (!$rating) {
            //     return response()->json([
            //         'status' => 'error',
            //         'message' => 'Đánh giá không tồn tại.'
            //     ], 204);
            // }

            // Validate input
            $validatedData = $request->validate([
                'reply' => 'required|string|max:1000',
            ], [
                'reply.required' => 'Vui lòng nhập nội dung trả lời.',
                'reply.string' => 'Nội dung trả lời phải là chuỗi ký tự.',
                'reply.max' => 'Nội dung trả lời không được vượt quá 1000 ký tự.',
            ]);

            // Thêm câu trả lời từ giảng viên
            $reply = RatingReply::create([
                'rating_id' => $rating->id,
                'user_id' => $user->id,  // ID giảng viên trả lời
                'reply' => $validatedData['reply'],
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Trả lời đánh giá thành công.',
                'data' => $reply
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Lỗi server.',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

}
