<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\RatingRequest;
use App\Models\Course;
use App\Models\Rating;
use App\Models\User;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function getRating(Request $request)
    {
        $courseId = $request->course_id;
        $countItem = 10;
        if ($request->page && $request->page != 0) {
            $countItem = $request->page * 10;
        }

        $listRating = DB::table('ratings as r')
            ->select(
                'u.name as user_name',
                'u.email as user_email',
                'u.avatar as user_avatar',
                'r.content as content',
                'r.rate as rate',
                'r.created_at as created_at'
            )
            ->join('users as u', 'u.id', '=', 'r.id_user')
            ->where('r.id_course', $courseId)
            ->orderByDesc('r.rate')
            ->limit($countItem)
            ->get();

        if (count($listRating) == 0) {
            return response()->json([
                'status' => 'error',
                'massage' => 'Không có đánh giá cho khóa học này'
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'data' => $listRating
        ], 200);
    }

    public function checkRating(Request $request)
    {
        $userId = $request->id_user;
        $courseId = $request->id_course;

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Khóa học không tồn tại'
            ]);
        }

        $checkProgressCourse = UserCourse::where('id_user', $userId)
            ->where('id_course', $courseId)
            ->first();

        if ($checkProgressCourse->progress_percent == 100 && $checkProgressCourse->completed_at) {
            return response()->json([
                'status' => 'success',
                'message' => 'Đã hoàn thành khóa học',
                'data' => [
                    'rating' => 'allow'
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Chưa hoàn thành khóa học',
            'data' => [
                'rating' => 'block'
            ]
        ], 200);
    }

    public function checkRated(Request $request)
    {
        $userId = $request->id_user;
        $courseId = $request->id_course;

        $user = User::find($userId);
        if (!$user) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Người dùng không tồn tại'
            ]);
        }

        $course = Course::find($courseId);
        if (!$course) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Khóa học không tồn tại'
            ]);
        }

        $checkRating = Rating::where('id_user', $userId)
            ->where('id_course', $courseId)
            ->first();

        if ($checkRating) {
            return response()->json([
                'status' => 'error',
                'message' => 'Đã đánh giá',
                'data' => [
                    'rated' => 'block'
                ]
            ], 200);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Chưa đánh giá',
            'data' => [
                'rated' => 'allow'
            ]
        ], 200);
    }

    public function addRating(RatingRequest $request)
    {
        $dataRating = $request->all();
        $newRating = Rating::query()->create($dataRating);
        if (!$newRating) {
            return response()->json([

                'status' => 'error',
                'message' => 'Đánh giá thất bại'
            ], 500);
        }

        return response()->json([

            'status' => 'success',
            'message' => 'Đánh giá thành công',
            'data' => $newRating
        ], 201);
    }

    public function getRatingHomePage()
    {
        $listRating = DB::table('ratings as r')
            ->select(
                'u.name as user_name',
                'u.email as user_email',
                'u.avatar as user_avatar',
                'r.content as content',
                'r.rate as rate',
                'r.created_at as created_at'
            )
            ->join('users as u', 'u.id', '=', 'r.id_user')
            ->where('r.rate', 5)
            ->orderByDesc('r.created_at')
            ->limit(6)
            ->get();

        if (count($listRating) == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'message' => 'Không có đánh giá'
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Danh sách đánh giá',
            'data' => $listRating
        ], 200);
    }
}
