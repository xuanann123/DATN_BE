<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\Courses\RatingRequest;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RatingController extends Controller
{
    public function getRating(Request $request)
    {
        $courseId = $request->course_id;
        $countItem = 10;
        if($request->page && $request->page != 0) {
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

        if(count($listRating) == 0) {
            return response()->json([
                'code' => 204,
                'status' => 'error',
                'massage' => 'Không có đánh giá cho khóa học này'
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'data' => $listRating
        ]);
    }

    public function addRating(RatingRequest $request){
        $dataRating = $request->all();
        $newRating = Rating::query()->create($dataRating);
        if(!$newRating) {
            return response()->json([
                'code' => 500,
                'status' => 'error',
                'message' => 'Đánh giá thất bại'
            ]);
        }

        return response()->json([
            'code' => 201,
            'status' => 'success',
            'message' => 'Đánh giá thành công',
            'data' => $newRating
        ]);
    }
}
