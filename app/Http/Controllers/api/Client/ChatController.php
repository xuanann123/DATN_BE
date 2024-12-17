<?php

namespace App\Http\Controllers\api\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function search(Request $request)
    {
        try {
            $user = auth()->user();
            $key = strtolower($request->query('search'));

            // search hoc vien
            $students = $this->searchUsers('user_course', 'course', 'id_user', $user->id, $key);

            // search giang vien
            $instructors = $this->searchUsers('courses', 'userCourses', 'id_user', $user->id, $key);

            return response()->json([
                'status' => 'success',
                'message' => 'Kết quả tìm kiếm.',
                'data' => [
                    'students' => $students,
                    'instructors' => $instructors,
                ],
            ], 200);
        } catch (\Exception $e) {
            // response lỗi
            return response()->json([
                'status' => 'error',
                'message' => 'Đã xảy ra lỗi khi tìm kiếm.',
                'error' => $e->getMessage() . '|' . $e->getLine(),
            ], 500);
        }
    }

    private function searchUsers($relation, $nestedRelation, $relationColumn, $userId, $key)
    {
        return User::whereHas($relation, function ($query) use ($nestedRelation, $relationColumn, $userId) {
            $query->whereHas($nestedRelation, function ($subQuery) use ($relationColumn, $userId) {
                $subQuery->where($relationColumn, $userId);
            });
        })
            ->where(function ($query) use ($key) {
                $query->where('name', 'LIKE', "%$key%");
            })
            ->select('id', 'name', 'avatar')
            ->get();
    }
}
